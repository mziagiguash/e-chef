<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizTranslation extends Model
{
    protected $fillable = ['quiz_id', 'locale', 'title'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
