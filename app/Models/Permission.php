<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    // Связь Many-to-Many с ролями
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
