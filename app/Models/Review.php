<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'rating',
        'course_id',
        'student_id'
    ];

    /**
     * Relationship with translations
     */
    public function translations()
    {
        return $this->hasMany(ReviewTranslation::class);
    }

    /**
     * Get display comment based on current locale
     */
    public function getDisplayCommentAttribute()
    {
        $currentLocale = app()->getLocale();

        if ($this->relationLoaded('translations') && $this->translations) {
            $translation = $this->translations->where('locale', $currentLocale)->first();
            if ($translation && !empty($translation->comment)) {
                return $translation->comment;
            }
        }

        // Fallback to original comment
        return $this->comment ?? '';
    }

    /**
     * Relationship with course
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Relationship with student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relationship with sender (student)
     */
    public function sender()
    {
        return $this->belongsTo(Student::class, 'sender_id');
    }

    /**
     * Relationship with receiver (instructor/course)
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
