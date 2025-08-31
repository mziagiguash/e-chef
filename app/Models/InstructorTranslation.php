<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorTranslation extends Model
{
     use HasFactory;

    protected $table = 'instructor_translations';
    public $timestamps = true; // т.к. в таблице есть created_at/updated_at

    protected $fillable = [
        'instructor_id', 'locale', 'name', 'designation', 'title', 'bio'
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
}
