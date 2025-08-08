<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategoryTranslation extends Model
{
    protected $fillable = ['CourseCategory_id', 'locale', 'name', 'description'];

    public function CourseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }
}