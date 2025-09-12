<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ← Добавляем
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory; // ← Добавляем этот трейт

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'total_questions',
        'correct_answers',
        'started_at',
        'completed_at',
        'time_taken',
        'status'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Добавляем accessors для вычисляемых атрибутов
    protected $appends = ['percentage', 'time_taken_formatted'];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class, 'attempt_id');
    }

    public function isPassed(): bool
    {
        return $this->score >= ($this->quiz->passing_score ?? 70);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->total_questions === 0) return 0;
        return round(($this->correct_answers / $this->total_questions) * 100, 2);
    }

    public function getTimeTakenFormattedAttribute(): string
    {
        if (!$this->time_taken) return '0s';

        $minutes = floor($this->time_taken / 60);
        $seconds = $this->time_taken % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }
}
