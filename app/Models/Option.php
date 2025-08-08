<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    protected $fillable = ['option_text', 'question_id'];



    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Translations
    public function translations()
    {
        return $this->hasMany(OptionTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->hasOne(OptionTranslation::class)->where('locale', $locale);
    }
}
