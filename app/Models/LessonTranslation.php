<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonTranslation extends Model
{
    use HasFactory;

    protected $table = 'lessons_translations'; // добавляем явное указание таблицы

    protected $fillable = [
        'lesson_id',
        'locale',
        'title',
        'description',
        'notes'
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
