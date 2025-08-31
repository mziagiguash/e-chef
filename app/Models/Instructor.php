<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

 protected $fillable = [
        // здесь реальные поля таблицы instructors
        'email','contact','role_id','status','access_block','password','image'
    ];

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

    public function getTranslation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->firstWhere('locale', $locale);
    }


public function frontShow(): string
{
    $name = $this->getTranslation()?->name ?? 'No Instructor';
    $designation = $this->getTranslation()?->designation ?? '';

    return $designation ? "$name — $designation" : $name;
}

}

