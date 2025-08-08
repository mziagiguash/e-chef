<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewTranslation extends Model
{
    protected $fillable = ['Review_id', 'locale', 'comment'];

    public function Review()
    {
        return $this->belongsTo(Review::class);
    }
}