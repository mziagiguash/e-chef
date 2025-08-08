<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizTranslation extends Model
{
    protected $fillable = ['Quiz_id', 'locale', 'title'];

    public function Quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}