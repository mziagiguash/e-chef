<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuizTranslation extends Model
{
    use HasFactory;

    protected $table = 'quizzes_translations'; // Правильное название таблицы

    protected $fillable = ['quiz_id', 'locale', 'title', 'description'];

    public $timestamps = true;
    
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
