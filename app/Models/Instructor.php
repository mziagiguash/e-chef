<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'contact',
        'role_id',
        'status',
        'password',
        'image',
        'language',
        'access_block',
    ];
public function getImageUrlAttribute()
{
    $imagePath = public_path('uploads/users');
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // если поле image заполнено и файл существует
    if ($this->image) {
        $file = $imagePath . '/' . $this->image;
        if (file_exists($file)) {
            return asset('uploads/users/' . $this->image);
        }
    }

    // ищем файл по id инструктора с разными расширениями
    foreach ($extensions as $ext) {
        $file = $imagePath . '/instructor_' . $this->id . '.' . $ext;
        if (file_exists($file)) {
            return asset('uploads/users/instructor_' . $this->id . '.' . $ext);
        }
    }

    // дефолтная картинка
    return asset('uploads/users/default-instructor.jpg');
}

    // связь с ролью
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // связь с курсами
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // мультиязычные переводы
    public function translations()
    {
        return $this->hasMany(InstructorTranslation::class, 'instructor_id');
    }

    // получить перевод по текущему языку
    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }
}

