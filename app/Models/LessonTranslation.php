<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTranslation extends Model
{
    protected $fillable = ['lesson_id', 'locale', 'title', 'description', 'notes'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
