<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quizzes';

    protected $fillable = [
        'lesson_id',
        'title', // ← ДОБАВИТЬ
        'questions_count', // ← ДОБАВИТЬ
        'time_limit',
        'passing_score',
        'max_attempts',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

public function attempts(): HasMany
{
    return $this->hasMany(QuizAttempt::class, 'quiz_id');
}

public function translations(): HasMany
{
    return $this->hasMany(QuizTranslation::class, 'quiz_id');
}

    // Accessor для получения перевода
    public function getTranslatedAttribute($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = config('app.fallback_locale', 'en');

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first();

        if (!$translation && $locale !== $defaultLocale) {
            $translation = $this->translations->where('locale', $defaultLocale)->first();
        }

        return $translation ?: new QuizTranslation();
    }

    // Для удобного отображения названия
public function getTitleAttribute()
{
    $locale = app()->getLocale();

    if (!$this->relationLoaded('translations')) {
        $this->load('translations');
    }

    $translation = $this->translations->where('locale', $locale)->first();

    // Безопасная проверка
    if ($translation && $translation->title) {
        return $translation->title;
    }

    // Безопасный доступ к первому переводу
    $firstTranslation = $this->translations->first();
    if ($firstTranslation && $firstTranslation->title) {
        return $firstTranslation->title;
    }

    return 'Quiz #' . ($this->id ?? 'new');
}

public function getDescriptionAttribute()
{
    $locale = app()->getLocale();

    if (!$this->relationLoaded('translations')) {
        $this->load('translations');
    }

    $translation = $this->translations->where('locale', $locale)->first();

    // Безопасная проверка - ИСПРАВЛЕНО!
    if ($translation && $translation->description) {
        return $translation->description;
    }

    // Безопасный доступ к первому переводу - ИСПРАВЛЕНО!
    $firstTranslation = $this->translations->first();
    if ($firstTranslation && $firstTranslation->description) {
        return $firstTranslation->description;
    }

    return null;
}
public function getTranslation(string $locale, string $field = 'title'): ?string
{
    if (!$this->relationLoaded('translations')) {
        $this->load('translations');
    }

    // Сначала ищем перевод для запрошенного языка
    $translation = $this->translations->where('locale', $locale)->first();

    // Если нет, ищем английский перевод
    if (!$translation && $locale !== 'en') {
        $translation = $this->translations->where('locale', 'en')->first();
    }

    // Если все еще нет, берем первый доступный перевод
    if (!$translation) {
        $translation = $this->translations->first();
    }

    return $translation ? $translation->{$field} : null;
}
}
