<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Проверяем, не оставлял ли пользователь уже отзыв
            $existingReview = Review::where('student_id', auth()->id())
                ->where('course_id', $request->course_id)
                ->first();

            if ($existingReview) {
                return redirect()->back()->with('error', __('You have already reviewed this course.'));
            }

            // Создаем отзыв
            $review = Review::create([
                'student_id' => auth()->id(),
                'course_id' => $request->course_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            // Создаем переводы
            $locales = ['en', 'ru', 'ka'];
            foreach ($locales as $locale) {
                $review->translations()->create([
                    'locale' => $locale,
                    'comment' => $request->comment,
                ]);
            }

            // Обновляем рейтинг курса
            $course = Course::find($request->course_id);
            $course->updateRating();

            DB::commit();

            return redirect()->back()->with('success', __('Thank you for your review!'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('Error submitting review. Please try again.'));
        }
    }

    public function index($courseId)
    {
        $course = Course::with(['reviews.student', 'reviews.translations'])
            ->findOrFail($courseId);

        $reviews = $course->reviews()->latest()->paginate(10);

        return view('frontend.courses.reviews', compact('course', 'reviews'));
    }
}
