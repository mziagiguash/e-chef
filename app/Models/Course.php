<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_category_id', 'instructor_id', 'type', 'price',
        'old_price', 'subscription_price', 'start_from', 'duration',
        'lesson', 'difficulty', 'course_code', 'tag', 'status',
        'language', 'thumbnail_video', 'image', 'thumbnail_image'
    ];

    // Связи
    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function material() { return $this->hasMany(Material::class); }
    public function quiz() { return $this->hasMany(Quiz::class); }
    public function review() { return $this->hasMany(Review::class); }
    public function discussion() { return $this->hasMany(Discussion::class); }
    public function enrollment() { return $this->hasMany(Enrollment::class); }
    public function lesson() { return $this->hasMany(Lesson::class); }

    // Переводы курса
    public function translations()
    {
        return $this->hasMany(CourseTranslation::class, 'course_id', 'id');
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    // Атрибут для текущего перевода
public function getTitleAttribute()
{
    return $this->translation()?->title; // возвращает строку, а не массив
}

public function getDescriptionAttribute()
{
    return $this->translation()?->description;
}

public function getPrerequisitesAttribute()
{
    return $this->translation()?->prerequisites;
}

public function getKeywordsAttribute()
{
    return $this->translation()?->keywords;
}

// Для категории
public function getCategoryNameAttribute()
{
    return $this->category?->translation()?->category_name;
}
public function getLocalizedTitleAttribute(): string
{
    return $this->title ?? '';
}
public function getTranslation($field, $locale = null)
{
    $locale = $locale ?: app()->getLocale();
    return $this->translation($locale)?->$field ?? '';
}

}

