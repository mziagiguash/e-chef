<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Checkout;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student_id = currentUserId();
        $student_info = Student::find($student_id);
        $enrollment = Enrollment::where('student_id', $student_id)->get();
        $course = Course::get();

        // 🔴 ИСПРАВЛЕНО: используем student_id вместо user_id
        $checkout = Checkout::where('student_id', $student_id)
                          ->latest()
                          ->get();

        return view('students.dashboard', compact(
            'student_info',
            'enrollment',
            'course',
            'checkout'
        ));
    }
}
