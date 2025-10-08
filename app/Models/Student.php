<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        // добавьте необходимые поля
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function answers()
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

 /**
     * Check if student has purchased a specific course
     */
    public function hasPurchasedCourse($courseId): bool
    {
        return $this->purchasedCourses()
            ->where('course_id', $courseId)
            ->exists();
    }

    /**
     * Relationship with purchased courses
     */
    public function purchasedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'student_courses')
            ->withPivot('purchased_at', 'purchase_price', 'status', 'progress', 'last_accessed_at')
            ->withTimestamps();
    }

    // Новые методы для прогресса уроков
    public function lessonProgress()
    {
        return $this->hasMany(StudentLessonProgress::class);
    }

   /**
     * Get progress for specific lesson
     */
    public function getLessonProgress(Lesson $lesson)
    {
        return $this->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->first();
    }

    /**
     * Update or create lesson progress
     */
    public function updateLessonProgress(Lesson $lesson, array $data)
    {
        $progressData = [
            'progress' => $data['progress'] ?? 0,
            'video_position' => $data['video_position'] ?? 0,
            'video_duration' => $data['video_duration'] ?? 0,
            'last_accessed_at' => now(),
            'course_id' => $lesson->course_id
        ];

        // Auto-complete if progress is 90% or more
        if (($data['progress'] ?? 0) >= 90) {
            $progressData['is_completed'] = true;
            $progressData['completed_at'] = now();
            $progressData['progress'] = 100;
        }

        return $this->lessonProgress()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            $progressData
        );
    }

    /**
     * Get completed lessons
     */
    public function completedLessons()
    {
        return $this->lessonProgress()->where('is_completed', true);
    }

    /**
     * Calculate course progress percentage
     */
    public function getCourseProgress(Course $course)
    {
        $totalLessons = $course->lessons()->count();
        if ($totalLessons == 0) return 0;

        $completedLessons = $this->lessonProgress()
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100);
    }

    /**
     * Check if lesson is completed
     */
    public function hasCompletedLesson(Lesson $lesson)
    {
        return $this->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->where('is_completed', true)
            ->exists();
    }

    /**
     * Get all completed lessons for a course
     */
    public function getCompletedLessonsForCourse(Course $course)
    {
        return $this->lessonProgress()
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->with('lesson')
            ->get();
    }

    // app/Models/Student.php

/**
 * Enroll student in a free course
 */
public function enrollInFreeCourse(Course $course)
{
    // Проверяем, не зачислен ли уже студент
    if ($this->hasPurchasedCourse($course->id)) {
        return false;
    }

    // Создаем запись о покупке курса
    $this->purchasedCourses()->attach($course->id, [
        'purchased_at' => now(),
        'purchase_price' => 0,
        'status' => 'completed',
        'progress' => 0,
        'last_accessed_at' => now()
    ]);

    // Создаем enrollment запись
    \App\Models\Enrollment::create([
        'student_id' => $this->id,
        'course_id' => $course->id,
        'payment_id' => null, // Нет платежа для бесплатного курса
        'amount_paid' => 0,
        'currency' => 'USD',
        'payment_method' => 'free',
        'payment_status' => 'completed',
        'transaction_id' => 'FREE_' . uniqid(),
        'enrollment_date' => now(),
    ]);

    return true;
}

/**
 * Check if student is enrolled in a course
 */

public function isEnrolled($courseId)
{
    return $this->enrollments()
        ->where('course_id', $courseId)
        ->where('payment_status', Enrollment::PAYMENT_COMPLETED)
        ->exists();
}
}
