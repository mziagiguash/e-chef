<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'locale',
        'title',
        'description',
        'prerequisites',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
