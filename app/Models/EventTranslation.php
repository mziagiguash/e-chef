<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
    protected $table = 'events_translations';

    protected $fillable = [
        'event_id',
        'locale',
        'title',
        'description',
        'topic',
        'goal',
        'location',
        'hosted_by'
        // created_at и updated_at удалены из fillable
    ];

    public $timestamps = false; // отключаем автоматические timestamps

    // Отношение к основному событию
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
