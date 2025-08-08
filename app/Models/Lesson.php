<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'course_id',
        'description',
        'notes',
    ];

    // Связь с курсом
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Связь с переводами
    public function translations()
    {
        return $this->hasMany(LessonTranslation::class);
    }

    // Перевод под текущую локаль
    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

}
