<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorTranslation extends Model
{
    use HasFactory;

    protected $table = 'instructor_translations';

    protected $fillable = [
        'instructor_id',
        'locale',
        'name',
        'bio',
        'title',
        'designation'
    ];

    public $timestamps = true;

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
