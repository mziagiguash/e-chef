<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTranslation extends Model
{
    use HasFactory;

    protected $table = 'course_translations';

    protected $fillable = [
        'course_id',
        'locale',
        'title',
        'description',
        'prerequisites',
        'keywords'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
