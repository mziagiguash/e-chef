<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model
{
    use HasFactory;

    protected $table = 'options';

    protected $fillable = [
        'question_id',
        'key', // добавлено поле key
        'is_correct',
        'order'
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(OptionTranslation::class, 'option_id');
    }

    // Accessor для получения текста опции на текущем языке
    public function getTextAttribute()
    {
        $locale = app()->getLocale();

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first();
        return $translation->text ?? $this->translations->first()->text ?? ''; // исправлено option_text на text
    }

    // Получение перевода для конкретного языка
public function getTranslation($locale = null, string $field = null): mixed
{
    $locale = $locale ?? app()->getLocale();
    $defaultLocale = config('app.fallback_locale', 'en');

    if (!$this->relationLoaded('translations')) {
        $this->load('translations');
    }

    // Ищем перевод
    $translation = $this->translations->where('locale', $locale)->first();

    if (!$translation && $locale !== $defaultLocale) {
        $translation = $this->translations->where('locale', $defaultLocale)->first();
    }

    if (!$translation) {
        $translation = $this->translations->first();
    }

    // Если запрошено конкретное поле
    if ($field && $translation) {
        return $translation->{$field} ?? null;
    }

    return $translation;
}
}
