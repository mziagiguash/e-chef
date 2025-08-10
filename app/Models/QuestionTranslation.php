<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTranslation extends Model
{
    protected $fillable = ['question_id', 'locale', 'content', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
