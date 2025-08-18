<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'title',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function courses(){
        return $this->hasMany(Course::class);
    }

public function translations()
{
    return $this->hasMany(InstructorTranslation::class, 'instructor_id');
}
public function getTranslationsDumpAttribute()
{
    return $this->translations->map(fn($t) => [
        'locale' => $t->locale,
        'name' => $t->name,
    ]);
}
public function getNameAttribute($value)
{
    $names = json_decode($value, true);
    $locale = app()->getLocale();
    return $names[$locale] ?? '';
}
}
