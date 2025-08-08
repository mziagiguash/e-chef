<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'locale',
        'title',
        'description',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
