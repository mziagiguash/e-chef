<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Models\UserNotification;

class NotificationService
{
    public static function create($data)
    {
        return UserNotification::create($data);
    }

public static function contactMessageReplied($contactMessage)
{
    \Log::info('=== NOTIFICATION SERVICE - CONTACT MESSAGE REPLY ===');
    \Log::info('Contact Message Details:', [
        'id' => $contactMessage->id,
        'sender_type' => $contactMessage->sender_type,
        'sender_id' => $contactMessage->sender_id,
        'name' => $contactMessage->name,
        'email' => $contactMessage->email
    ]);

    // 🔴 ИСПРАВЛЕНО: Если sender_type = null, пытаемся найти студента по email
    if ($contactMessage->sender_type === 'student' && $contactMessage->sender_id) {
        // Случай 1: Есть sender_type и sender_id
        return self::createNotificationForStudent($contactMessage->sender_id, $contactMessage);
    }
    elseif (empty($contactMessage->sender_type) && !empty($contactMessage->email)) {
        // Случай 2: sender_type = null, но есть email - ищем студента
        return self::createNotificationByEmail($contactMessage);
    }
    else {
        \Log::warning('❌ CANNOT CREATE NOTIFICATION - No student identification', [
            'sender_type' => $contactMessage->sender_type,
            'sender_id' => $contactMessage->sender_id,
            'email' => $contactMessage->email
        ]);
        return null;
    }
}

private static function createNotificationForStudent($studentId, $contactMessage)
{
    try {
        $notification = self::create([
            'student_id' => $studentId,
            'type' => 'contact_message_replied',
            'title' => 'Response to: ' . $contactMessage->subject,
            'message' => 'An administrator has responded to your contact message.',
            'contact_message_id' => $contactMessage->id,
            'data' => [
                'contact_message_id' => $contactMessage->id,
                'subject' => $contactMessage->subject,
                'admin_response' => $contactMessage->admin_notes
            ]
        ]);

        \Log::info('✅ NOTIFICATION CREATED FOR STUDENT ID', [
            'notification_id' => $notification->id,
            'student_id' => $studentId
        ]);

        return $notification;

    } catch (\Exception $e) {
        \Log::error('❌ ERROR CREATING NOTIFICATION FOR STUDENT: ' . $e->getMessage());
        return null;
    }
}

private static function createNotificationByEmail($contactMessage)
{
    // Ищем студента по email
    $student = \App\Models\Student::where('email', $contactMessage->email)->first();

    if ($student) {
        \Log::info('Found student by email', [
            'email' => $contactMessage->email,
            'student_id' => $student->id,
            'student_name' => $student->name
        ]);

        return self::createNotificationForStudent($student->id, $contactMessage);
    } else {
        \Log::warning('No student found with email: ' . $contactMessage->email);
        return null;
    }
}
}
