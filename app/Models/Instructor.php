<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

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
    return $this->hasMany(InstructorTranslation::class);
}


public function translation($locale = null)
{
    $locale = $locale ?: app()->getLocale();
    return $this->translations()->where('locale', $locale)->first();
}
}
