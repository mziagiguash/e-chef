<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionTranslation extends Model
{
    protected $fillable = ['discussion_id', 'locale', 'title', 'content'];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}
