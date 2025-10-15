<?php
// app/Models/ContactMessage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'name',
        'email',
        'subject',
        'message',
        'status',
        'admin_notes',
        'assigned_admin_id',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    // Отношение со студентом
    public function student()
    {
        return $this->belongsTo(Student::class, 'sender_id')->where('sender_type', 'student');
    }

    // Отношение с инструктором
    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'sender_id')->where('sender_type', 'instructor');
    }

    // Универсальное отношение с отправителем
    public function sender()
    {
        if ($this->sender_type === 'student') {
            return $this->belongsTo(Student::class, 'sender_id');
        } elseif ($this->sender_type === 'instructor') {
            return $this->belongsTo(Instructor::class, 'sender_id');
        }

        // Возвращаем пустое отношение для гостей
        return $this->belongsTo(Student::class, 'sender_id')->whereRaw('1 = 0');
    }

    // Отношение с админом
    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    // Scopes для фильтрации
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeFromStudent($query)
    {
        return $query->where('sender_type', 'student');
    }

    public function scopeFromInstructor($query)
    {
        return $query->where('sender_type', 'instructor');
    }

    // Helpers
    public function markAsResolved()
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);
    }
// Добавляем методы-хелперы
public function getResolvedAtFormattedAttribute()
{
    return $this->resolved_at ? $this->resolved_at->format('M d, Y H:i') : 'Not resolved';
}

public function getCreatedAtFormattedAttribute()
{
    return $this->created_at->format('M d, Y H:i');
}

public function getResolvedAtDiffForHumansAttribute()
{
    return $this->resolved_at ? $this->resolved_at->diffForHumans() : null;
}

    public function isNew()
    {
        return $this->status === 'new';
    }

    // Получаем имя отправителя
    public function getSenderDisplayNameAttribute()
    {
        if ($this->sender_type === 'student' && $this->relationLoaded('student') && $this->student) {
            return $this->student->name . ' (Student)';
        } elseif ($this->sender_type === 'instructor' && $this->relationLoaded('instructor') && $this->instructor) {
            return $this->instructor->name . ' (Instructor)';
        }

        return $this->name . ' (Guest)';
    }

    // Получаем email отправителя
    public function getSenderDisplayEmailAttribute()
    {
        if ($this->sender_type === 'student' && $this->relationLoaded('student') && $this->student) {
            return $this->student->email;
        } elseif ($this->sender_type === 'instructor' && $this->relationLoaded('instructor') && $this->instructor) {
            return $this->instructor->email;
        }

        return $this->email;
    }
}
