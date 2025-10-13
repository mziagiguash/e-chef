<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_category_id',
        'instructor_id',
        'courseType',
        'coursePrice',
        'courseOldPrice',
        'subscription_price',
        'start_from',
        'duration',
        'lesson',
        'course_code',
        'thumbnail_video_url',
        'tag',
        'status',
        'image',
        'thumbnail_image',
        'thumbnail_video_file'
    ];

    protected $dates = [
        'start_from',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'coursePrice' => 'decimal:2',
        'courseOldPrice' => 'decimal:2',
        'subscription_price' => 'decimal:2',
        'duration' => 'integer',
        'lesson' => 'integer',
        'status' => 'integer'
    ];

    // Связи
    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_courses')
            ->withPivot('purchased_at', 'purchase_price', 'status', 'progress', 'last_accessed_at')
            ->withTimestamps();
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ДОБАВЛЯЕМ НЕДОСТАЮЩИЕ МЕТОДЫ ДЛЯ ПРОГРЕССА
    public function getCompletedLessonsCount($studentId)
    {
        return \App\Models\LessonProgress::whereHas('lesson', function($query) {
                $query->where('course_id', $this->id);
            })
            ->where('student_id', $studentId)
            ->where('is_completed', true)
            ->count();
    }

    public function getTotalLessonsCount()
    {
        return $this->lessons()->count();
    }

    public function hasPurchasedCourse($studentId)
    {
        return $this->students()->where('student_id', $studentId)->exists();
    }

    public function hasCompletedCourse($studentId)
    {
        $totalLessons = $this->getTotalLessonsCount();
        $completedLessons = $this->getCompletedLessonsCount($studentId);

        return $totalLessons > 0 && $completedLessons >= $totalLessons;
    }

    public function getCourseProgress($studentId)
    {
        $totalLessons = $this->getTotalLessonsCount();
        $completedLessons = $this->getCompletedLessonsCount($studentId);

        if ($totalLessons === 0) {
            return 0;
        }

        return round(($completedLessons / $totalLessons) * 100);
    }

    public function canReviewCourse($studentId)
    {
        // Проверяем, купил ли студент курс
        $hasPurchased = $this->hasPurchasedCourse($studentId);

        // Проверяем, завершил ли курс
        $hasCompleted = $this->hasCompletedCourse($studentId);

        // Проверяем, не оставлял ли уже отзыв
        $hasReviewed = \App\Models\Review::where('student_id', $studentId)
            ->where('course_id', $this->id)
            ->exists();

        return $hasPurchased && $hasCompleted && !$hasReviewed;
    }

    // Существующие методы
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getEnrollmentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    public function translations()
    {
        return $this->hasMany(CourseTranslation::class, 'course_id');
    }

    // ... остальные существующие методы остаются без изменений
    public function getTranslationModel($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }

    public function getTranslation($locale, $field = 'title')
    {
        $translation = $this->translations->where('locale', $locale)->first();

        if ($translation && isset($translation->{$field})) {
            return $translation->{$field};
        }

        $fallback = $this->translations->where('locale', 'en')->first();
        return $fallback->{$field} ?? '';
    }

    public function getNextLesson($currentLessonOrder)
    {
        return $this->lessons()
            ->where('order', '>', $currentLessonOrder)
            ->orderBy('order')
            ->first();
    }

    public function getPrevLesson($currentLessonOrder)
    {
        return $this->lessons()
            ->where('order', '<', $currentLessonOrder)
            ->orderBy('order', 'desc')
            ->first();
    }

    // Accessors
    public function getTitleAttribute()
    {
        return $this->getTranslationValue('title') ?? 'No Title';
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslationValue('description') ?? '';
    }

    public function getPrerequisitesAttribute()
    {
        return $this->getTranslationValue('prerequisites') ?? '';
    }

    public function getKeywordsAttribute()
    {
        return $this->getTranslationValue('keywords') ?? '';
    }

    protected function getTranslationValue($field, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->firstWhere('locale', $locale);

        if (!$translation) {
            $translation = $this->translations->firstWhere('locale', 'en');
        }

        if (!$translation) {
            $translation = $this->translations->first();
        }

        return $translation ? $translation->$field : null;
    }

    public function getPriceAttribute()
    {
        return $this->coursePrice;
    }

    public function getOldPriceAttribute()
    {
        return $this->courseOldPrice;
    }

    public function getTypeAttribute()
    {
        return $this->courseType;
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            0 => 'Pending',
            1 => 'Inactive',
            2 => 'Active',
            default => 'Unknown'
        };
    }

    public function getFormattedStartFromAttribute()
    {
        if (!$this->start_from) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($this->start_from)->format('Y-m-d');
        } catch (\Exception $e) {
            return $this->start_from;
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 2);
    }

    public function scopeWithTranslations($query, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $query->with(['translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }]);
    }

    public function scopeWithAllTranslations($query)
    {
        return $query->with('translations');
    }
    public function getRouteKeyName()
{
    return 'id'; // или другое поле, если используется slug
}
}
