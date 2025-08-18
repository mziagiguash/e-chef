<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'course_categories_translations';
    protected $fillable = ['instructor_id', 'locale', 'name', 'bio', 'designation'];

    

    public function instructor()
{
    return $this->belongsTo(Instructor::class);
}

}
