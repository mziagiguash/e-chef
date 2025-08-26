<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id', 'locale', 'name', 'designation', 'title', 'bio'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
