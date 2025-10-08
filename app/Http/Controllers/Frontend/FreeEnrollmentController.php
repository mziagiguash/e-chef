<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class FreeEnrollmentController extends Controller
{
    public function enroll($locale, Course $course)
    {
        try {
            // Проверяем, что курс бесплатный
            if ($course->course_type !== 'free' && $course->price > 0) {
                return redirect()->back()->with('error', __('This course is not free'));
            }

            // Получаем или создаем студента
            $student = $this->getOrCreateStudent();
            if (!$student) {
                return redirect()->route('login')->with('error', __('Please login to enroll in courses'));
            }

            // Зачисляем на курс
            $enrolled = $this->enrollStudent($student, $course);

            if ($enrolled) {
                return redirect()->route('frontend.courses.show', [
                    'locale' => $locale,
                    'course' => $course->id
                ])->with('success', __('Successfully enrolled in the course!'));
            } else {
                return redirect()->back()->with('info', __('You are already enrolled in this course'));
            }

        } catch (\Exception $e) {
            \Log::error('Free enrollment error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Enrollment failed. Please try again.'));
        }
    }

    /**
     * Get or create student for current user
     */
    private function getOrCreateStudent()
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();

        // Если у пользователя уже есть student_id
        if ($user->student_id) {
            return Student::find($user->student_id);
        }

        // Создаем нового студента
        $student = Student::create();
        $user->student_id = $student->id;
        $user->save();

        return $student;
    }

    /**
     * Enroll student in course
     */
    private function enrollStudent(Student $student, Course $course)
    {
        // Проверяем, не зачислен ли уже
        if ($student->isEnrolled($course->id)) {
            return false;
        }

        // Создаем запись в student_courses
        $student->purchasedCourses()->attach($course->id, [
            'purchased_at' => now(),
            'purchase_price' => 0,
            'status' => 'completed',
            'progress' => 0,
            'last_accessed_at' => now()
        ]);

        // Создаем запись в enrollments
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'payment_id' => null,
            'amount_paid' => 0,
            'currency' => 'USD',
            'payment_method' => 'free',
            'payment_status' => 'completed',
            'transaction_id' => 'FREE_' . uniqid() . '_' . time(),
            'enrollment_date' => now(),
        ]);

        return true;
    }
}
