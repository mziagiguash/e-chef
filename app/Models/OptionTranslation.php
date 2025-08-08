<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionTranslation extends Model
{
    protected $fillable = ['option_id', 'locale', 'option_text'];

public function option()
{
    return $this->belongsTo(Option::class);
}

}
