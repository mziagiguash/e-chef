<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkout_id', // Добавлено
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'transaction_id',
        'payer_email',
        'payer_name',
        'payment_data'
    ];

    protected $casts = [
        'payment_data' => 'array',
        'amount' => 'decimal:2'
    ];

    // Статусы платежей
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    // Методы оплаты
    const METHOD_SSLCOMMERZ = 'sslcommerz';
    const METHOD_STRIPE = 'stripe';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_BANK = 'bank_transfer';
    const METHOD_CARD = 'credit_card';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkout()
    {
        return $this->belongsTo(Checkout::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scope для фильтрации
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', self::STATUS_PENDING);
    }
}
