<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function myCourses($locale)
    {
        try {
            $user = auth()->user();

            if (!$user || !$user->student_id) {
                return redirect()->route('login')->with('error', __('Please login to view your courses'));
            }

            $student = Student::with([
                'purchasedCourses.translations',
                'purchasedCourses.instructor.translations',
                'purchasedCourses.lessons'
            ])->find($user->student_id);

            if (!$student) {
                return redirect()->back()->with('error', __('Student profile not found'));
            }

            $courses = $student->purchasedCourses;

            // Добавляем прогресс для каждого курса
            $courses->each(function($course) use ($student) {
                $course->progress = $student->getCourseProgress($course);
                $course->completed_lessons = $student->lessonProgress()
                    ->where('course_id', $course->id)
                    ->where('is_completed', true)
                    ->count();
                $course->total_lessons = $course->lessons->count();
            });

            return view('frontend.student.dashboard', compact('courses', 'locale'));

        } catch (\Exception $e) {
            \Log::error('My courses error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Error loading your courses'));
        }
    }
}
