<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'course_categories_translations';

    protected $fillable = ['course_category_id', 'locale', 'category_name'];

    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }
}
