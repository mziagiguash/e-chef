<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTranslation extends Model
{
    protected $fillable = ['Question_id', 'locale', 'question_text'];

    public function Question()
    {
        return $this->belongsTo(Question::class);
    }
}