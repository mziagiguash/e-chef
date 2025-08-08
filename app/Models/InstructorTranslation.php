<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorTranslation extends Model
{
    protected $fillable = ['Instructor_id', 'locale', 'name', 'bio', 'description'];

    public function Instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}