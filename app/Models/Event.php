<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
protected $fillable = ['title', 'description', 'date', 'location'];

public function translations()
{
    return $this->hasMany(EventTranslation::class);
}

public function translation($locale = null)
{
    $locale = $locale ?: app()->getLocale();
    return $this->translations()->where('locale', $locale)->first();
}
}
