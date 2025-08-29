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

    protected $casts = [
        'title' => 'array',        // JSON для всех языков
        'description' => 'array',
        'notes' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Получение перевода по атрибуту
    public function getTranslation(string $attribute, ?string $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        $data = $this->{$attribute} ?? [];

        if (!is_array($data)) {
            $data = json_decode($data, true) ?? [];
        }

        return $data[$locale] ?? reset($data) ?? '';
    }

    // Для удобного отображения
    public function displayTitle($locale = null)
    {
        return $this->getTranslation('title', $locale);
    }

    public function displayDescription($locale = null)
    {
        return $this->getTranslation('description', $locale);
    }

    public function displayNotes($locale = null)
    {
        return $this->getTranslation('notes', $locale);
    }
}
