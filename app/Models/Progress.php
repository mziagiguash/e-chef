<?php
// app/Models/Progress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Progress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'progress_percentage',
        'completed',
        'last_viewed_material_id',
        'last_viewed_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'last_viewed_at' => 'datetime',
        'progress_percentage' => 'decimal:2'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lastViewedMaterial()
    {
        return $this->belongsTo(Material::class, 'last_viewed_material_id');
    }
}
