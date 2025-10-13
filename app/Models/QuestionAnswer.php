<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionAnswer extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
        'text_answer',
        'rating_answer',
        'is_correct',
        'points_earned'
        // user_id УДАЛЕН
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    // Метод user() УДАЛЕН - так как поля нет в БД

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }

    // Дополнительный метод для доступа к студенту через попытку
    public function getStudentAttribute()
    {
        return $this->attempt->student;
    }
}
