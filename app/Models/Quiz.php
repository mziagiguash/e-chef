<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'quizzes';

    protected $fillable = [
        'lesson_id',
        'order',
        'is_active',
        'time_limit',
        'passing_score',
        'max_attempts'
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
        return $this->hasMany(QuizAttempt::class);
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
        return $translation->title ??
               $this->translations->first()->title ??
               'Quiz #' . $this->id;
    }

    // Для удобного отображения описания
    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first();
        return $translation->description ??
               $this->translations->first()->description ??
               null;
    }
}
