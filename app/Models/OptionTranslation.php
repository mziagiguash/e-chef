<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionTranslation extends Model
{
    protected $fillable = [
        'option_id',
        'locale',
        'text'
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
