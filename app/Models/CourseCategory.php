<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_status',
        'category_image'
    ];

    protected $casts = [
        'category_status' => 'integer'
    ];

    // Связи
    public function translations()
    {
        return $this->hasMany(CourseCategoryTranslation::class, 'course_category_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'course_category_id');
    }

public function getTranslation($field = null, $locale = null)
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

    if (!$translation) {
        return $field ? null : null;
    }

    return $field ? $translation->$field : $translation;
}

public function getTranslatedCategoryNameAttribute()
{
    return $this->getTranslation('category_name') ?? 'No Category';
}

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('category_status', 1);
    }

    public function scopeWithTranslations($query, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $query->with(['translations' => function($q) use ($locale) {
            $q->where('locale', $locale);
        }]);
    }
}
