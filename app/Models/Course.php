<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function translations()
    {
        return $this->hasMany(CourseTranslation::class, 'course_id');
    }

    public function getTranslationModel($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
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
}
