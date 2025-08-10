<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'price', 'image',
        'description',
        'prerequisites',
    ];
    public function courses()
{
    return $this->hasMany(\App\Models\Course::class, 'course_category_id', 'id');
}

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function material()
    {
        return $this->hasMany(Material::class);
    }

    public function quiz()
    {
        return $this->hasMany(Quiz::class);
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function discussion()
    {
        return $this->hasMany(Discussion::class);
    }

    public function enrollment()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function lesson()
    {
        return $this->hasMany(Lesson::class);
    }
public function getCurrentTranslationAttribute()
{
    return $this->translation();
}

    // Все переводы
   public function translations()
{
    return $this->hasMany(CourseTranslation::class, 'course_id', 'id');
}

public function translation($locale = null)
{
    $locale = $locale ?: app()->getLocale();
    return $this->translations()->where('locale', $locale)->first();
}

}
