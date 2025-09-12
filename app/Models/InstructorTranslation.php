<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorTranslation extends Model
{
    use HasFactory;

    protected $table = 'instructor_translations';

    protected $fillable = [
        'instructor_id',
        'locale',
        'name',
        'bio',
        'designation',
        'title'
    ];

    // Добавляем casts для правильного отображения типов данных
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
