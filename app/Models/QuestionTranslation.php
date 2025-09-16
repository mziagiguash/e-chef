<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuestionTranslation extends Model
{
    use HasFactory;
    protected $table = 'questions_translations';
    protected $fillable = [
        'question_id',
        'locale',
        'content' 
    ];

    public $timestamps = true;

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
