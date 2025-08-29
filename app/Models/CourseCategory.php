<?php

// app/Models/CourseCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'category_status', 'category_image'];

protected $casts = [
    'category_name' => 'array', // Laravel сам декодирует JSON в массив
];


    public function courses()
    {
        return $this->hasMany(Course::class, 'course_category_id', 'id');
    }


public function translations()
{
    return $this->hasMany(CourseCategoryTranslation::class, 'course_category_id');
}

public function getTranslationsDumpAttribute()
{
    return $this->translations->map(fn($t) => [
        'locale' => $t->locale,
        'category_name' => $t->category_name,
    ]);
}

// Получение перевода поля в указанной локали
    public function getTranslation(string $field, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        if (!isset($this->$field)) {
            return '';
        }

        $value = $this->$field;

        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? reset($value);
        }

        return (string) $value;
    }

    // Для совместимости с Blade: $cat->localized_name
    public function getLocalizedNameAttribute(): string
    {
        return $this->getTranslation('category_name');
    }


public function translation($locale = null)
{
    $locale = $locale ?: app()->getLocale();
    return $this->translations()->where('locale', $locale)->first();
}

}
