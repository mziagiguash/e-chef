<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // Новые методы для прогресса уроков
    public function lessonProgress()
    {
        return $this->hasMany(StudentLessonProgress::class);
    }

    public function getLessonProgress(Lesson $lesson)
    {
        return $this->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->first();
    }

    public function updateLessonProgress(Lesson $lesson, array $data)
    {
        $progressData = [
            'progress' => $data['progress'],
            'video_position' => $data['video_position'] ?? 0,
            'video_duration' => $data['video_duration'] ?? 0,
            'last_accessed_at' => now(),
            'course_id' => $lesson->course_id // убедимся что course_id установлен
        ];

        // Если прогресс 100% или больше, отмечаем как завершенный
        if ($data['progress'] >= 100) {
            $progressData['is_completed'] = true;
            $progressData['completed_at'] = now();
        }

        return $this->lessonProgress()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            $progressData
        );
    }

    public function completedLessons()
    {
        return $this->lessonProgress()->where('is_completed', true);
    }

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

    public function hasCompletedLesson(Lesson $lesson)
    {
        return $this->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->where('is_completed', true)
            ->exists();
    }
}
