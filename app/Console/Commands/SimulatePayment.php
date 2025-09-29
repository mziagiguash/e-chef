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
    protected $signature = 'simulate:payment {count=5}';
    protected $description = 'Simulate successful payments and create enrollments';

    public function handle()
    {
        $count = $this->argument('count');

        // Получаем случайных студентов и курсы
        $students = Student::inRandomOrder()->limit($count)->get();
        $courses = Course::inRandomOrder()->limit($count)->get();

        if ($students->isEmpty()) {
            $this->error('No students found in database!');
            return;
        }

        if ($courses->isEmpty()) {
            $this->error('No courses found in database!');
            return;
        }

        $this->info("Simulating {$count} payments...");

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {
                $student = $students[$i % $students->count()];
                $course = $courses[$i % $courses->count()];

                // Случайная сумма от 10 до 500
                $amount = rand(1000, 50000) / 100; // от 10.00 до 500.00

                // Случайный метод оплаты
                $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'sslcommerz'];
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // Случайный статус (80% completed, 15% pending, 5% failed)
                $statusRand = rand(1, 100);
                if ($statusRand <= 80) {
                    $paymentStatus = 'completed';
                } elseif ($statusRand <= 95) {
                    $paymentStatus = 'pending';
                } else {
                    $paymentStatus = 'failed';
                }

                // Создаем платеж
                $payment = Payment::create([
                    'student_id' => $student->id,
                    'amount' => $amount,
                    'currency' => 'USD',
                    'currency_code' => 'USD',
                    'method' => $paymentMethod,
                    'txnid' => 'TXN_' . uniqid() . '_' . time(),
                    'status' => $this->mapPaymentStatus($paymentStatus),
                ]);

                // Создаем зачисление
                Enrollment::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'payment_id' => $payment->id,
                    'amount_paid' => $paymentStatus === 'completed' ? $amount : 0,
                    'currency' => 'USD',
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'transaction_id' => $payment->txnid,
                    'enrollment_date' => now(),
                ]);

                $this->info("Created enrollment #" . ($i + 1) . ": {$student->name} → {$course->title} (\${$amount}, {$paymentStatus})");
            }

            DB::commit();
            $this->info("Successfully created {$count} simulated enrollments!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
        }
    }

    private function mapPaymentStatus($status)
    {
        $statusMap = [
            'pending' => 0,
            'completed' => 1,
            'failed' => 2,
            'refunded' => 3
        ];

        return $statusMap[$status] ?? 0;
    }
}
