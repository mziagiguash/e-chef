<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Option extends Model
{
    use HasFactory; // Убираем SoftDeletes

    protected $table = 'options';

    protected $fillable = [
        'question_id',
        'is_correct',
        'order'
    ];

    // Убираем deleted_at, так как его нет в таблице
    // protected $dates = ['deleted_at'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(OptionTranslation::class, 'option_id');
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

        return $translation ?: new OptionTranslation();
    }


    public function getTranslation(string $locale, string $field = 'option_text'): ?string
    {
        $translation = $this->translations->where('locale', $locale)->first();
        return $translation ? $translation->{$field} : null;
    }

}
