<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

protected $fillable = [
    'course_id',
    'lesson_id',
    'type',
    'content',
    'content_url',
];

protected $casts = [
    'course_id' => 'integer',
    'lesson_id' => 'integer',
];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

 public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function translations()
    {
        return $this->hasMany(MaterialTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    // Аксессор: заголовок (берётся из translation->title)
    public function getTitleAttribute()
    {
        return $this->translation()?->title ?? '';
    }

    // Аксессор: текст контента для текущей локали
    public function getContentTextAttribute()
    {
        // Для совместимости: сначала content_text, если нет — старое content
        return $this->translation()?->content_text
            ?? $this->translation()?->content
            ?? '';
    }

    // Удобный метод для получения url к файлу (если нужно)
    public function getContentFileUrlAttribute()
    {
        if ($this->content && file_exists(public_path('uploads/courses/contents/' . $this->content))) {
            return asset('uploads/courses/contents/' . $this->content);
        }
        return null;
    }
}
