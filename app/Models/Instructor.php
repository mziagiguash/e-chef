<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $casts = [
        'name'        => 'array',
        'designation' => 'array',
        'title'       => 'array',
        'bio'         => 'array',
    ];

    public function getImageUrlAttribute()
    {
        $imagePath = public_path('uploads/users');
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if ($this->image) {
            $file = $imagePath . '/' . $this->image;
            if (file_exists($file)) {
                return asset('uploads/users/' . $this->image);
            }
        }

        foreach ($extensions as $ext) {
            $file = $imagePath . '/instructor_' . $this->id . '.' . $ext;
            if (file_exists($file)) {
                return asset('uploads/users/instructor_' . $this->id . '.' . $ext);
            }
        }

        return asset('uploads/users/default-instructor.jpg');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function translations()
    {
        return $this->hasMany(InstructorTranslation::class, 'instructor_id');
    }

    public function translate($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first();
    }


    /**
     * Автоматически возвращаем локализованное имя
     */
    public function getLocalizedNameAttribute()
    {
        if (is_array($this->name)) {
            return $this->name[app()->getLocale()]
                ?? $this->name['en']
                ?? '';
        }
        return $this->name ?? '';
    }
public function getTranslation($field, $locale = null)
{
    $locale = $locale ?: app()->getLocale();
    $decoded = is_array($this->$field) ? $this->$field : json_decode($this->$field, true);
    return $decoded[$locale] ?? reset($decoded) ?? '';
}

}
