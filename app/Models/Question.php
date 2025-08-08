<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = ['question_text', 'quiz_id'];


    public function quiz(){
        return $this->belongsTo(Quiz::class);
    }

    public function option(){
        return $this->hasMany(Option::class);
    }

    public function answer()
    {
        return $this->hasMany(Answer::class);
    }

    // Translations
    public function translations()
    {
        return $this->hasMany(QuestionTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->hasOne(QuestionTranslation::class)->where('locale', $locale);
    }
}
