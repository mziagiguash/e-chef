<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quiz_id',
        'type',
        'points',
        'order',
        'is_required',
        'max_choices',
        'min_rating',
        'max_rating'
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(QuestionTranslation::class);
    }
public function getTranslation(string $field = 'content', string $locale = null): ?string
{
    $locale = $locale ?? app()->getLocale();

    if (!$this->relationLoaded('translations')) {
        $this->load('translations');
    }

    $translation = $this->translations->where('locale', $locale)->first();

    if (!$translation && $locale !== 'en') {
        $translation = $this->translations->where('locale', 'en')->first();
    }

    if (!$translation) {
        $translation = $this->translations->first();
    }

    return $translation ? $translation->{$field} : null;
}

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function correctOptions(): HasMany
    {
        return $this->options()->where('is_correct', true);
    }

}
