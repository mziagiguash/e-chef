<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'materials_translations';

    protected $fillable = [
        'material_id',
        'locale',
        'title',
        'content',      // прежнее поле (если у вас оно есть и используется)
        'content_text', // новое поле для переводимого текстового контента
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
