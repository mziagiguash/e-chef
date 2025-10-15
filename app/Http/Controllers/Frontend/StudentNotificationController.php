<?php
// app/Http/Controllers/Frontend/StudentNotificationController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StudentNotificationController extends Controller
{
    /**
     * Отладочный метод для проверки уведомлений
     */
    public function debugNotifications()
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return response()->json(['error' => 'Student not logged in'], 401);
        }

        try {
            $hasTable = Schema::hasTable('user_notifications');
            $notifications = $hasTable ?
                UserNotification::where('student_id', $student_id)->get() :
                collect();

            return response()->json([
                'student_id' => $student_id,
                'has_user_notifications_table' => $hasTable,
                'total_notifications' => $notifications->count(),
                'unread_count' => $hasTable ?
                    UserNotification::where('student_id', $student_id)->where('is_read', false)->count() : 0,
                'notifications' => $notifications->take(5),
                'table_structure' => $hasTable ? Schema::getColumnListing('user_notifications') : []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'student_id' => $student_id
            ], 500);
        }
    }

    /**
     * Получить все уведомления студента
     */
    public function getNotifications()
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        $notifications = UserNotification::where('student_id', $student_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($notifications);
    }

    /**
     * Пометка уведомления как прочитанного
     */
    public function markAsRead($notificationId)
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        $notification = UserNotification::where('id', $notificationId)
            ->where('student_id', $student_id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Пометка всех уведомлений как прочитанных
     */
    public function markAllAsRead()
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        UserNotification::where('student_id', $student_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Получить количество непрочитанных уведомлений
     */
    public function getUnreadCount()
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        $unreadCount = UserNotification::where('student_id', $student_id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
    public function markAsRead($notificationId)
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        $notification = UserNotification::where('id', $notificationId)
            ->where('student_id', $student_id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        // Помечаем как прочитанное
        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'notification' => $notification
        ]);
    }

    /**
     * Пометка всех уведомлений как прочитанных
     */
    public function markAllAsRead()
    {
        $student_id = session('userId') ? encryptor('decrypt', session('userId')) : null;

        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        UserNotification::where('student_id', $student_id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }
}
