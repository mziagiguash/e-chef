<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialTranslation extends Model
{
    protected $fillable = ['material_id', 'locale', 'title', 'content'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
