<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; // если используете мягкое удаление

class Event extends Model
{
    use SoftDeletes; // если нужно

    protected $fillable = [
        'title',          // теперь nullable
        'description',    // теперь nullable
        'location',       // теперь nullable
        'topic',          // теперь nullable
        'goal',           // теперь nullable
        'hosted_by',      // теперь nullable
        'image',
        'date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $dates = ['deleted_at']; // если используете мягкое удаление

    public function translations(): HasMany
    {
        return $this->hasMany(EventTranslation::class, 'event_id');
    }

    // Accessor для получения перевода
    public function getTranslatedAttribute($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = config('app.fallback_locale', 'en');

        // Загружаем переводы если они еще не загружены
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first();

        // Если нет перевода для текущего языка, используем дефолтный
        if (!$translation && $locale !== $defaultLocale) {
            $translation = $this->translations->where('locale', $defaultLocale)->first();
        }

        return $translation ?: new EventTranslation();
    }

    // Динамические accessors
    public function getTitleAttribute()
    {
        if (array_key_exists('title', $this->attributes) && $this->attributes['title'] !== null) {
            return $this->attributes['title'];
        }
        return $this->translated->title ?? '';
    }

    public function getDescriptionAttribute()
    {
        if (array_key_exists('description', $this->attributes) && $this->attributes['description'] !== null) {
            return $this->attributes['description'];
        }
        return $this->translated->description ?? '';
    }

    public function getLocationAttribute()
    {
        if (array_key_exists('location', $this->attributes) && $this->attributes['location'] !== null) {
            return $this->attributes['location'];
        }
        return $this->translated->location ?? '';
    }

    public function getTopicAttribute()
    {
        if (array_key_exists('topic', $this->attributes) && $this->attributes['topic'] !== null) {
            return $this->attributes['topic'];
        }
        return $this->translated->topic ?? '';
    }

    public function getGoalAttribute()
    {
        if (array_key_exists('goal', $this->attributes) && $this->attributes['goal'] !== null) {
            return $this->attributes['goal'];
        }
        return $this->translated->goal ?? '';
    }

    public function getHostedByAttribute()
    {
        if (array_key_exists('hosted_by', $this->attributes) && $this->attributes['hosted_by'] !== null) {
            return $this->attributes['hosted_by'];
        }
        return $this->translated->hosted_by ?? '';
    }
}
