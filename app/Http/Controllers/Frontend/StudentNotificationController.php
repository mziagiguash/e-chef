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
          $student_id = encryptor('decrypt', session('userId'));
    session(['student_id' => $student_id]);
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
          $student_id = encryptor('decrypt', session('userId'));
    session(['student_id' => $student_id]);
        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        $notifications = UserNotification::where('student_id', $student_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($notifications);
    }
    /**
     * Получить количество непрочитанных уведомлений
     */
    public function getUnreadCount()
    {
          $student_id = encryptor('decrypt', session('userId'));
    session(['student_id' => $student_id]);
        if (!$student_id) {
            return response()->json(['error' => 'Student not found'], 401);
        }

        $unreadCount = UserNotification::where('student_id', $student_id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }
/**
     * Пометка одного уведомления как прочитанного
     */
    public function markAsRead($id)
    {
        try {
            // Просто находим и обновляем уведомление
            $notification = UserNotification::find($id);

            if (!$notification) {
                return response()->json(['success' => false, 'error' => 'Notification not found'], 404);
            }

            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Пометка всех уведомлений как прочитанных
     */
    public function markAllAsRead(Request $request)
    {
        try {
            // Получаем ID студента из сессии
            $student_id = session('student_id');

            // Если нет в сессии, пробуем расшифровать
            if (!$student_id && session('userId')) {
                $student_id = encryptor('decrypt', session('userId'));
            }

            if (!$student_id) {
                return response()->json(['success' => false, 'error' => 'Student not found'], 401);
            }

            // Простое обновление всех непрочитанных уведомлений студента
            $updated = UserNotification::where('student_id', $student_id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
