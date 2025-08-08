<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTranslation extends Model
{
    protected $fillable = [
        'user_id', 'locale', 'name', 'contact'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
