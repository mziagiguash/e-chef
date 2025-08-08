<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
   protected $fillable = ['event_id', 'locale', 'title', 'description'];

public function event()
{
    return $this->belongsTo(Event::class);
}

}
