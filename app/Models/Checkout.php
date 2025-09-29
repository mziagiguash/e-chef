<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_amount',
        'currency',
        'payment_method',
        'status',
        'billing_address',
        'cart_data'
    ];

    protected $casts = [
        'cart_data' => 'array',
        'billing_address' => 'array',
        'total_amount' => 'decimal:2'
    ];

    // Статусы заказов
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'checkout_course')
                    ->withTimestamps();
    }

    // Scope для фильтрации по статусу
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
