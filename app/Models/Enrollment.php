<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'payment_id',
        'amount_paid',
        'currency',
        'payment_method',
        'payment_status',
        'transaction_id',
        'payment_date',
        'payment_data',
        'enrollment_date'
    ];

    protected $casts = [
        'payment_data' => 'array',
        'amount_paid' => 'decimal:2',
        'enrollment_date' => 'datetime',
        'payment_date' => 'datetime'
    ];

    // Статусы платежей
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_COMPLETED = 'completed';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    // Методы оплаты
    const METHOD_SSLCOMMERZ = 'sslcommerz';
    const METHOD_CARD = 'credit_card';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_BANK = 'bank_transfer';

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Scope методы
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', self::PAYMENT_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', self::PAYMENT_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', self::PAYMENT_FAILED);
    }

    // Хелпер методы
    public function isPaid()
    {
        return $this->payment_status === self::PAYMENT_COMPLETED;
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $statuses = [
            self::PAYMENT_PENDING => 'warning',
            self::PAYMENT_COMPLETED => 'success',
            self::PAYMENT_FAILED => 'danger',
            self::PAYMENT_REFUNDED => 'info'
        ];

        return '<span class="badge badge-'.$statuses[$this->payment_status].'">'.ucfirst($this->payment_status).'</span>';
    }
}
