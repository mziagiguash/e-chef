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

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
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

    public function getTranslatedPrerequisitesAttribute()
    {
        return $this->getTranslationValue('prerequisites');
    }

    public function getTranslatedKeywordsAttribute()
    {
        return $this->getTranslationValue('keywords');
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
     public function getTranslation($field = null, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        // Загружаем переводы если еще не загружены
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->firstWhere('locale', $locale);

        if (!$translation) {
            // Fallback на английский
            $translation = $this->translations->firstWhere('locale', 'en');
        }

        if (!$translation) {
            // Fallback на первый доступный перевод
            $translation = $this->translations->first();
        }

        if (!$translation) {
            return $field ? null : null;
        }

        return $field ? $translation->$field : $translation;
    }

    // Добавим также accessor для удобства
    public function getTranslatedTitleAttribute()
    {
        return $this->getTranslation('title') ?? 'No Title';
    }
    public function getTitleAttribute()
{
    $locale = app()->getLocale();

    if (!$this->relationLoaded('translations')) {
        $this->load('translations');
    }

    $translation = $this->translations->where('locale', $locale)->first();
    return $translation->title ??
           $this->translations->first()->title ??
           'No title';
}

    public function getTranslatedDescriptionAttribute()
    {
        return $this->getTranslation('description') ?? '';
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
}
