<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes, HasFactory;

    const TYPE_SINGLE = 'single';
    const TYPE_MULTIPLE = 'multiple';
    const TYPE_TEXT = 'text';
    const TYPE_RATING = 'rating';

    protected $fillable = [
        'quiz_id',
        'type',
        'order',
        'points',
        'is_required',
        'max_choices',
        'min_rating',
        'max_rating'
    ];

    protected $dates = ['deleted_at'];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function correctOptions(): HasMany
    {
        return $this->hasMany(Option::class)->where('is_correct', true);
    }

    // Accessor для получения перевода контента
    public function getContentAttribute()
    {
        $locale = app()->getLocale();

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first();
        return $translation->content ??
               $this->translations->first()->content ??
               '';
    }

    // Accessor для удобства (синоним для content)
    public function getTextAttribute()
    {
        return $this->content;
    }

    public function isMultipleChoice(): bool
    {
        return in_array($this->type, [self::TYPE_SINGLE, self::TYPE_MULTIPLE]);
    }

    public function isTextType(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    public function isRatingType(): bool
    {
        return $this->type === self::TYPE_RATING;
    }

    // Получение перевода для конкретного поля
    public function getTranslation($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first();
        return $translation ? $translation->$field : $this->$field;
    }
}
