<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategoryTranslation extends Model
{

    use HasFactory;
    protected $table = 'course_categories_translations'; // Добавьте эту строку


    protected $fillable = [
        'course_category_id',
        'locale',
        'category_name'
    ];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }
}
