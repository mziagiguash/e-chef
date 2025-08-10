<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTranslation extends Model

{
    protected $table = 'courses_translations';

    protected $fillable = ['course_id', 'locale', 'title', 'description', 'prerequisites'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
