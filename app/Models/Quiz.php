<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'lesson_id'];

public function quiz()
{
    return $this->belongsTo(Quiz::class);
}

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function question()
    {
        return $this->hasMany(Question::class);
    }

    // Translations
    public function translations()
    {
        return $this->hasMany(QuizTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        return $this->hasOne(QuizTranslation::class)->where('locale', $locale);
    }
}
