<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FreeCheckoutController extends Controller
{
    public function processFreeCheckout(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            if (!$user) {
                return redirect()->route('login')->with('error', __('Please login to enroll in courses'));
            }

            // Получаем или создаем студента
            $student = $this->getOrCreateStudent($user);
            if (!$student) {
                return redirect()->back()->with('error', __('Failed to create student profile'));
            }

            // Получаем курсы из корзины (сессии)
            $cart = session('cart', []);
            $enrolledCourses = [];

            foreach ($cart as $courseId => $details) {
                $course = Course::find($courseId);

                if ($course && ($course->course_type === 'free' || $course->price == 0)) {
                    // Зачисляем на бесплатный курс
                    if ($this->enrollStudent($student, $course)) {
                        $enrolledCourses[] = $course;
                    }
                }
            }

            if (empty($enrolledCourses)) {
                DB::rollBack();
                return redirect()->back()->with('error', __('No free courses found in your cart'));
            }

            // Очищаем корзину
            session()->forget('cart');
            session()->forget('coupon_applied');
            session()->forget('coupon_code');
            session()->forget('discount');

            DB::commit();

            return redirect()->route('student.courses', ['locale' => app()->getLocale()])
                ->with('success', __('Successfully enrolled in ') . count($enrolledCourses) . __(' courses!'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Free checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Enrollment failed. Please try again.'));
        }
    }

    /**
     * Get or create student for user
     */
    private function getOrCreateStudent($user)
    {
        if ($user->student_id) {
            return Student::find($user->student_id);
        }

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
