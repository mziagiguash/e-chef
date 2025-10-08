<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'student_lesson_progress';

    protected $fillable = [
        'student_id',
        'lesson_id',
        'course_id',
        'progress',
        'video_position',
        'video_duration',
        'is_completed',
        'completed_at',
        'last_accessed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress' => 'integer',
        'video_position' => 'integer',
        'video_duration' => 'integer'
    ];

    /**
     * Relationship with Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relationship with Lesson
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Relationship with Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope for completed lessons
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope for specific course
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope for specific student
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Mark lesson as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'is_completed' => true,
            'progress' => 100,
            'completed_at' => now(),
            'last_accessed_at' => now()
        ]);
    }

    /**
     * Update progress
     */
    public function updateProgress($progress, $videoPosition = null, $videoDuration = null)
    {
        $data = [
            'progress' => min(100, max(0, $progress)),
            'last_accessed_at' => now()
        ];

        if ($videoPosition !== null) {
            $data['video_position'] = $videoPosition;
        }

        if ($videoDuration !== null) {
            $data['video_duration'] = $videoDuration;
        }

        // Auto-complete if progress is 90% or more
        if ($progress >= 90) {
            $data['is_completed'] = true;
            $data['completed_at'] = now();
            $data['progress'] = 100;
        }

        $this->update($data);
    }
}
