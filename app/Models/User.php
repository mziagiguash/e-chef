<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes;

    // The attributes that are mass assignable.
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // The attributes that should be cast.
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // relation with role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function instructors()
    {
        $this->belongsTo(Instructor::class);
    }

    public function discussion()
    {
        return $this->hasMany(Discussion::class);
    }

    public function message()
    {
        return $this->hasMany(Message::class);
    }
 public function canReviewCourse($courseId)
    {
        // Проверяем, купил ли пользователь курс
        $hasPurchased = $this->hasPurchasedCourse($courseId);

        // Проверяем, завершил ли курс
        $hasCompleted = $this->hasCompletedCourse($courseId);

        // Проверяем, не оставлял ли уже отзыв
        $hasReviewed = \App\Models\Review::where('student_id', $this->id)
            ->where('course_id', $courseId)
            ->exists();

        return $hasPurchased && $hasCompleted && !$hasReviewed;
    }

    /**
     * Check if user has purchased the course
     */

public function hasPurchasedCourse($courseId): bool
{
    if (!$this->student_id) {
        return false;
    }

    $student = Student::find($this->student_id);
    return $student ? $student->isEnrolled($courseId) : false;
}
    /**
     * Check if user has completed the course
     */
    public function hasCompletedCourse($courseId)
    {
        $progress = \App\Models\Progress::where('student_id', $this->id)
            ->where('course_id', $courseId)
            ->first();

        return $progress && $progress->completed;
    }

    /**
     * Get course progress percentage
     */
    public function getCourseProgress($courseId)
    {
        $progress = \App\Models\Progress::where('student_id', $this->id)
            ->where('course_id', $courseId)
            ->first();

        return $progress ? $progress->progress_percentage : 0;
    }

    /**
     * Relationship with enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Relationship with reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'student_id');
    }

    /**
     * Relationship with progress
     */
    public function progress()
    {
        return $this->hasMany(Progress::class, 'student_id');
    }
}
