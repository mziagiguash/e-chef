<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    public function course(){
        return $this->hasMany(Course::class);
    }

    // Translations
    public function translations()
    {
        return $this->hasMany(CourseCategoryTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->hasOne(CourseCategoryTranslation::class)->where('locale', $locale);
    }
}