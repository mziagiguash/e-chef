<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionTranslation extends Model
{
    use HasFactory;

    protected $table = 'options_translations';

    protected $fillable = [
        'option_id',
        'locale',
        'option_text' // Исправлено
    ];
    public $timestamps = true;
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }
}
