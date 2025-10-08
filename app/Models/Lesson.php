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
        'quiz_id', // ← добавить
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'title',
        'description',
        'notes',
        'has_quiz',
        'materials_count',
        'material_types'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Отношение с переводами
    public function translations(): HasMany
    {
        return $this->hasMany(LessonTranslation::class);
    }

    // Отношение с материалами и их переводами
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class)->with('translations');
    }

    // Правильное отношение с квизом (через lesson_id в quizzes)
    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'lesson_id');
    }

    // Универсальный метод для получения перевода (для accessors)
    protected function getTranslatedField(string $field): ?string
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

    // Метод для blade шаблона с указанием локали
    public function getTranslation(string $locale, string $field = 'title'): ?string
    {
        if (!$this->relationLoaded('translations')) {
            $this->load('translations');
        }

        $translation = $this->translations->where('locale', $locale)->first();

        if (!$translation) {
            // Fallback на английский
            $translation = $this->translations->where('locale', 'en')->first();
        }

        if (!$translation) {
            // Fallback на первый доступный перевод
            $translation = $this->translations->first();
        }

        return $translation ? $translation->{$field} : null;
    }

   public function getDisplayTitleAttribute()
    {
        $currentLocale = app()->getLocale();

        // Если есть переводы и отношение translations
        if ($this->relationLoaded('translations') && $this->translations) {
            $translation = $this->translations->where('locale', $currentLocale)->first();
            if ($translation && !empty($translation->title)) {
                return $translation->title;
            }
        }

        // Fallback на основное название или ID
        return $this->title ?? "Lesson #{$this->id}";
    }

    // Accessors
    public function getTitleAttribute()
    {
        return $this->getTranslatedField('title') ?? 'Lesson #' . $this->id;
    }

    public function getDescriptionAttribute()
    {
        return $this->getTranslatedField('description');
    }

    public function getNotesAttribute()
    {
        return $this->getTranslatedField('notes');
    }

    // Проверка наличия квиза
    public function getHasQuizAttribute(): bool
    {
        return $this->quiz()->exists();
    }

    // Количество материалов в уроке
    public function getMaterialsCountAttribute(): int
    {
        return $this->materials->count();
    }

    // Типы материалов в уроке
    public function getMaterialTypesAttribute(): array
    {
        return $this->materials->pluck('type')->unique()->toArray();
    }
}
