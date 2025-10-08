<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimulatePayment extends Command
{
    protected $signature = 'simulate:payment {count=5} {--student-id=} {--list-students}';
    protected $description = 'Simulate successful payments and create enrollments';

    public function handle()
    {
        // Опция для показа всех студентов
        if ($this->option('list-students')) {
            $this->listAllStudents();
            return;
        }

        $count = $this->argument('count');
        $studentId = $this->option('student-id');

        // Если указан конкретный студент, используем его, иначе случайных
        if ($studentId) {
            $student = Student::find($studentId);

            if (!$student) {
                $this->error("Student with ID '{$studentId}' not found!");
                $this->line("Available students:");
                $this->listAllStudents();
                return;
            }

            $students = collect([$student]);
            $this->info("Using specific student ID: {$student->id}");
        } else {
            $students = Student::inRandomOrder()->limit($count)->get();
            if ($students->isEmpty()) {
                $this->error('No students found in database!');
                return;
            }
        }

        $courses = Course::inRandomOrder()->limit($count)->get();
        if ($courses->isEmpty()) {
            $this->error('No courses found in database!');
            return;
        }

        $this->info("Simulating {$count} payments...");

        DB::beginTransaction();
        try {
            $enrollmentsCreated = 0;

            for ($i = 0; $i < $count; $i++) {
                $student = $students[$i % $students->count()];
                $course = $courses[$i % $courses->count()];

                // Проверяем, не зачислен ли уже студент на этот курс
                $existingEnrollment = Enrollment::where('student_id', $student->id)
                    ->where('course_id', $course->id)
                    ->exists();

                if ($existingEnrollment) {
                    $this->warn("Student ID {$student->id} is already enrolled in course '{$course->title}'. Skipping...");
                    continue;
                }

                // Сумма от 10 до 500
                $amount = rand(1000, 50000) / 100;

                // Метод оплаты
                $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'sslcommerz'];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // Создаем платеж
                $payment = Payment::create([
                    'student_id' => $student->id,
                    'amount' => $amount,
                    'currency' => 'USD',
                    'currency_code' => 'USD',
                    'method' => $paymentMethod,
                    'txnid' => 'TXN_' . uniqid() . '_' . time(),
                    'status' => 1, // completed
                ]);

                // Создаем зачисление
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'payment_id' => $payment->id,
                    'amount_paid' => $amount,
                    'currency' => 'USD',
                    'payment_method' => $paymentMethod,
                    'payment_status' => 'completed',
                    'transaction_id' => $payment->txnid,
                    'enrollment_date' => now(),
                ]);

                $enrollmentsCreated++;
                $this->line("Created enrollment #" . ($enrollmentsCreated) . ": <fg=green>Student ID {$student->id} → {$course->title} (\${$amount})</>");
            }

            DB::commit();
            $this->info("Successfully created {$enrollmentsCreated} enrollments!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }
    }

    private function listAllStudents()
    {
        $students = Student::all();

        if ($students->isEmpty()) {
            $this->error("No students found in database!");
            return;
        }

        $this->info("Available students ({$students->count()}):");

        $students->each(function ($student) {
            $this->line("Student ID: {$student->id}, Created: {$student->created_at->format('Y-m-d')}");
        });
    }
}
