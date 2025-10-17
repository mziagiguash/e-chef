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
        'parent_id',
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

    // 🔴 ИСПРАВЛЕНО: Простые отношения без условий where
    public function student()
    {
        return $this->belongsTo(Student::class, 'sender_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'sender_id');
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    // 🔴 ДОБАВЛЕНО: Отношения для переписки
    public function parent()
    {
        return $this->belongsTo(ContactMessage::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ContactMessage::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    // 🔴 ДОБАВЛЕНО: Безопасные методы доступа
    public function getSafeStudentAttribute()
    {
        return $this->sender_type === 'student' ? $this->student : null;
    }

    public function getSafeInstructorAttribute()
    {
        return $this->sender_type === 'instructor' ? $this->instructor : null;
    }

    public function getSenderDisplayNameAttribute()
    {
        if ($this->sender_type === 'student' && $this->safe_student) {
            return $this->safe_student->name . ' (Student)';
        } elseif ($this->sender_type === 'instructor' && $this->safe_instructor) {
            return $this->safe_instructor->name . ' (Instructor)';
        } elseif ($this->sender_type) {
            return ucfirst($this->sender_type) . ($this->sender_id ? ' (ID: ' . $this->sender_id . ')' : '');
        } else {
            return $this->name . ' (Guest)';
        }
    }

    public function getSenderDisplayEmailAttribute()
    {
        if ($this->sender_type === 'student' && $this->safe_student) {
            return $this->safe_student->email;
        } elseif ($this->sender_type === 'instructor' && $this->safe_instructor) {
            return $this->safe_instructor->email;
        } else {
            return $this->email;
        }
    }

    // 🔴 ДОБАВЛЕНО: Метод для получения всей истории переписки
    public function getConversationHistoryAttribute()
    {
        $history = collect();

        // Находим корневое сообщение
        $rootMessage = $this;
        while ($rootMessage->parent) {
            $rootMessage = $rootMessage->parent;
        }

        // Добавляем корневое сообщение
        $history->push($rootMessage);

        // Добавляем все ответы
        $replies = ContactMessage::where('parent_id', $rootMessage->id)
            ->orWhere(function($query) use ($rootMessage) {
                $query->where('parent_id', $this->id)
                      ->where('id', '!=', $rootMessage->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $history = $history->merge($replies);

        return $history->unique('id')->sortBy('created_at');
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeMainMessages($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
