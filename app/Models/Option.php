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
        return $translation->option_text ?? $this->translations->first()->option_text ?? '';
    }

    // Получение перевода для конкретного языка
    public function getTranslation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return $this->translations->where('locale', $locale)->first();
    }
}
