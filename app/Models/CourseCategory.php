<?php

// app/Models/CourseCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_status', 'category_image'];
    protected $casts = [
    'category_name' => 'array',
];


    public function courses()
    {
        return $this->hasMany(Course::class, 'course_category_id', 'id');
    }


public function translations()
{
    return $this->hasMany(CourseCategoryTranslation::class, 'course_category_id');
}
public function getTranslationsDumpAttribute()
{
    return $this->translations->map(fn($t) => [
        'locale' => $t->locale,
        'category_name' => $t->category_name,
    ]);
}

}
