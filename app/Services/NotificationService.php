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
        \Log::info('=== CREATING USER NOTIFICATION ===');
        \Log::info('Contact Message Details:', [
            'id' => $contactMessage->id,
            'sender_type' => $contactMessage->sender_type,
            'sender_id' => $contactMessage->sender_id
        ]);

        if ($contactMessage->sender_type === 'student' && $contactMessage->sender_id) {
            $notification = self::create([
                'student_id' => $contactMessage->sender_id,
                'type' => 'contact_message_replied',
                'title' => 'Response to: ' . $contactMessage->subject,
                'message' => 'An administrator has responded to your contact message. Click to view details.',
                'contact_message_id' => $contactMessage->id,
                'data' => [
                    'contact_message_id' => $contactMessage->id,
                    'subject' => $contactMessage->subject,
                    'admin_response' => $contactMessage->admin_notes
                ]
            ]);

            \Log::info('✅ USER NOTIFICATION CREATED SUCCESSFULLY', [
                'notification_id' => $notification->id,
                'student_id' => $notification->student_id
            ]);

            return $notification;
        }

        \Log::warning('❌ CANNOT CREATE NOTIFICATION - Not a student message');
        return null;
    }

    
    public static function courseUpdate($studentId, $course, $message)
    {
        return self::create([
            'student_id' => $studentId,
            'type' => 'course_update',
            'title' => 'Course Update: ' . $course->title,
            'message' => $message,
            'data' => [
                'course_id' => $course->id,
                'course_title' => $course->title
            ]
        ]);
    }
}
