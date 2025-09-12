<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'quiz_id',
    ];

    protected $dates = ['deleted_at'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Отношение с переводами
    public function translations(): HasMany
    {
        return $this->hasMany(LessonTranslation::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    // Правильное отношение с квизом (через lesson_id в quizzes)
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'lesson_id');
    }

    // Упрощенный accessor для перевода
    protected function getTranslation($field)
    {
        $locale = app()->getLocale();
        $defaultLocale = config('app.fallback_locale', 'en');

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first()
                    ?? $this->translations->where('locale', $defaultLocale)->first();

        return $translation->{$field} ?? null;
    }

    public function getTitleAttribute()
    {
        return $this->getTranslation('title') ?? 'Lesson #' . $this->id;
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslation('description');
    }

    public function getNotesAttribute()
    {
        return $this->getTranslation('notes');
    }

    // Проверка наличия квиза
    public function getHasQuizAttribute(): bool
    {
        return $this->quiz()->exists();
    }
}
