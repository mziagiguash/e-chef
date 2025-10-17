<?php
// app/Http\Controllers\Backend\Communication\ContactMessageController.php

namespace App\Http\Controllers\Backend\Communication;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;


class ContactMessageController extends Controller
{

public function index(Request $request)
{
    // Только корневые сообщения
    $query = ContactMessage::whereNull('parent_id')
        ->orderBy('created_at', 'desc');

    if ($request->has('status') && in_array($request->status, ['new', 'in_progress', 'resolved'])) {
        $query->where('status', $request->status);
    }

    $contactMessages = $query->paginate(10);

    // 🔴 ИСПРАВЛЕНО: Не добавляем вычисляемые атрибуты в модель
    // Вместо этого создаем массив с данными для отображения
    $messagesForDisplay = $contactMessages->getCollection()->map(function ($message) {
        $message->load(['student', 'instructor']);

        return [
            'message' => $message,
            'sender_display_name' => $this->getSafeSenderName($message),
            'sender_display_email' => $this->getSafeSenderEmail($message)
        ];
    });

    $stats = [
        'new' => ContactMessage::whereNull('parent_id')->where('status', 'new')->count(),
        'in_progress' => ContactMessage::whereNull('parent_id')->where('status', 'in_progress')->count(),
        'resolved' => ContactMessage::whereNull('parent_id')->where('status', 'resolved')->count(),
        'total' => ContactMessage::whereNull('parent_id')->count(),
    ];

    return view('backend.communication.contact-message.index', compact('contactMessages', 'messagesForDisplay', 'stats'));
}


public function show($id)
{
    $contactMessage = ContactMessage::findOrFail($id);

    // Загружаем отношения безопасно
    if ($contactMessage->sender_type === 'student') {
        $contactMessage->load('student');
    } elseif ($contactMessage->sender_type === 'instructor') {
        $contactMessage->load('instructor');
    }

    // 🔴 УДАЛЕНО: Не сохраняем вычисляемые атрибуты в модель
    // Вместо этого передаем их отдельно в view

    if ($contactMessage->status == 'new') {
        $contactMessage->update(['status' => 'in_progress']);
    }

    // 🔴 ИСПРАВЛЕНО: Передаем вычисляемые значения отдельно
    $safeSenderName = $this->getSafeSenderName($contactMessage);
    $safeSenderEmail = $this->getSafeSenderEmail($contactMessage);

    return view('backend.communication.contact-message.show', compact(
        'contactMessage',
        'safeSenderName',
        'safeSenderEmail'
    ));
}
// 🔴 ДОБАВЛЕНО: Вспомогательные методы для безопасного доступа
private function getSafeSenderName($contactMessage)
{
    if ($contactMessage->sender_type === 'student') {
        if ($contactMessage->student) {
            return $contactMessage->student->name . ' (Student)';
        } else {
            return $contactMessage->name . ' (Student - Deleted)';
        }
    } elseif ($contactMessage->sender_type === 'instructor') {
        if ($contactMessage->instructor) {
            return $contactMessage->instructor->name . ' (Instructor)';
        } else {
            return $contactMessage->name . ' (Instructor - Deleted)';
        }
    } elseif ($contactMessage->sender_type) {
        return $contactMessage->name . ' (' . ucfirst($contactMessage->sender_type) . ')';
    } else {
        return $contactMessage->name . ' (Guest)';
    }
}

private function getSafeSenderEmail($contactMessage)
{
    if ($contactMessage->sender_type === 'student' && $contactMessage->student) {
        return $contactMessage->student->email;
    } elseif ($contactMessage->sender_type === 'instructor' && $contactMessage->instructor) {
        return $contactMessage->instructor->email;
    } else {
        return $contactMessage->email;
    }
}
    /**
     * Update the status of the message.
     */


public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:new,in_progress,resolved',
        'admin_notes' => 'nullable|string|max:1000'
    ]);

    $contactMessage = ContactMessage::findOrFail($id);

    $updateData = ['status' => $request->status];

    if ($request->status == 'resolved') {
        $updateData['resolved_at'] = now();
    }

    if ($request->filled('admin_notes')) {
        $updateData['admin_notes'] = $request->admin_notes;

        // Отправляем уведомление студенту
        NotificationService::contactMessageReplied($contactMessage);
    }

    $contactMessage->update($updateData);

    return redirect()->back()->with('success', 'Message status updated successfully.');
}

// 🔴 ИСПРАВЛЕННЫЙ МЕТОД: Обновляет существующий диалог
public function continueConversation(Request $request)
{
    $request->validate([
        'parent_id' => 'required|exists:contact_messages,id',
        'message' => 'required|string|min:10|max:5000',
        'subject' => 'required|string|max:255'
    ]);

    // 🔴 ИСПРАВЛЕНО: Используем проверку студента
    $studentAuth = $this->checkStudentAuth();
    if (!$studentAuth instanceof Student) {
        return $studentAuth; // Возвращаем редирект или JSON ошибку
    }

    $student_id = $studentAuth->id;

    try {
        $parentMessage = ContactMessage::findOrFail($request->parent_id);

        // 🔴 ИСПРАВЛЕНО: Находим корневое сообщение диалога
        $rootMessage = $parentMessage;
        while ($rootMessage->parent_id) {
            $rootMessage = ContactMessage::find($rootMessage->parent_id);
            if (!$rootMessage) break;
        }

        // 🔴 ИСПРАВЛЕНО: Проверяем, что студент имеет доступ к этому диалогу
        $isOwner = $rootMessage->sender_id === $student_id && $rootMessage->sender_type === 'student';
        if (!$isOwner) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to continue this conversation'
                ], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to continue this conversation');
        }

        // Создаем новое сообщение как продолжение диалога
        $newMessage = ContactMessage::create([
            'sender_id' => $student_id,
            'sender_type' => 'student',
            'parent_id' => $rootMessage->id, // 🔴 ВАЖНО: Привязываем к корневому сообщению
            'name' => $studentAuth->name,
            'email' => $studentAuth->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'in_progress'
        ]);

        // 🔴 ИСПРАВЛЕНО: Обновляем статус КОРНЕВОГО сообщения
        $rootMessage->update([
            'status' => 'in_progress',
            'resolved_at' => null
        ]);

        \Log::info('✅ CONVERSATION CONTINUED BY STUDENT', [
            'root_message_id' => $rootMessage->id,
            'new_message_id' => $newMessage->id,
            'student_id' => $student_id,
            'student_name' => $studentAuth->name
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Your reply has been sent successfully! The conversation has been reopened.'
            ]);
        }

        return redirect()->route('student.my-messages')->with('success', 'Your reply has been sent successfully!');

    } catch (\Exception $e) {
        \Log::error('Error continuing conversation: ' . $e->getMessage());

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', 'Error sending message: ' . $e->getMessage());
    }
}

public function myMessages()
{
    // 🔴 ИСПРАВЛЕНО: Используем проверку студента
    $studentAuth = $this->checkStudentAuth();
    if (!$studentAuth instanceof Student) {
        return $studentAuth; // Возвращаем редирект или JSON ошибку
    }

    $student_id = $studentAuth->id;

    try {
        // 🔴 ИСПРАВЛЕННЫЙ ЗАПРОС: Находим только корневые сообщения студента
        $conversations = ContactMessage::where('sender_id', $student_id)
            ->where('sender_type', 'student')
            ->whereNull('parent_id') // Только корневые сообщения
            ->with(['replies' => function($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        \Log::info('✅ MY MESSAGES LOADED', [
            'student_id' => $student_id,
            'conversations_count' => $conversations->count()
        ]);

        return view('students.my-messages', ['messages' => $conversations]);

    } catch (\Exception $e) {
        \Log::error('❌ ERROR LOADING MESSAGES: ' . $e->getMessage());

        return view('students.my-messages', ['messages' => collect()])
            ->with('error', 'Error loading messages: ' . $e->getMessage());
    }
}
    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    $contactMessage = ContactMessage::findOrFail($id);
    $contactMessage->delete();

    // 🔴 ИСПРАВЛЕНО: Используем правильное имя маршрута
    return redirect()->route('contact-messages.index')
        ->with('success', 'Message deleted successfully.');
}
public function sendResponse(Request $request, $id)
{
    \Log::info('=== SEND RESPONSE FORM SUBMITTED ===', [
        'contact_message_id' => $id,
        'form_data' => $request->except(['_token']),
        'url' => $request->fullUrl()
    ]);

    $request->validate([
        'response_subject' => 'required|string|max:255',
        'response_message' => 'required|string|min:10|max:5000',
        'also_send_email' => 'nullable|boolean'
    ]);

    $contactMessage = ContactMessage::findOrFail($id);

    \Log::info('=== CONTACT MESSAGE FOUND ===', [
        'contact_message_id' => $contactMessage->id,
        'sender_type' => $contactMessage->sender_type,
        'sender_id' => $contactMessage->sender_id,
        'student_id' => $contactMessage->sender_type === 'student' ? $contactMessage->sender_id : null,
        'current_admin_notes' => $contactMessage->admin_notes,
        'current_status' => $contactMessage->status
    ]);

    // Сохраняем ответ как admin_notes
    $contactMessage->update([
        'admin_notes' => $request->response_message,
        'status' => 'resolved',
        'resolved_at' => $contactMessage->resolved_at ?? now()
    ]);

    \Log::info('=== CONTACT MESSAGE UPDATED ===', [
        'contact_message_id' => $contactMessage->id,
        'new_admin_notes' => $contactMessage->admin_notes,
        'new_status' => $contactMessage->status,
        'resolved_at' => $contactMessage->resolved_at
    ]);

    // СОЗДАЁМ УВЕДОМЛЕНИЕ ДЛЯ СТУДЕНТА
    \Log::info('=== CALLING NOTIFICATION SERVICE ===');
    $notification = NotificationService::contactMessageReplied($contactMessage);

    if ($notification) {
        \Log::info('✅ NOTIFICATION CREATED SUCCESSFULLY', [
            'notification_id' => $notification->id,
            'student_id' => $notification->student_id,
            'title' => $notification->title
        ]);
    } else {
        \Log::error('❌ NOTIFICATION SERVICE RETURNED NULL - No notification created');
    }

    return redirect()->back()->with('success', 'Response sent to student successfully!');
}

private function sendResponseEmail($contactMessage, $subject, $message)
{
    try {
        $email = $contactMessage->sender_type === 'student' && $contactMessage->student
            ? $contactMessage->student->email
            : $contactMessage->email;

        Mail::send('emails.contact-response', [
            'studentName' => $contactMessage->name,
            'adminMessage' => $message,
            'originalMessage' => $contactMessage->message,
            'subject' => $subject
        ], function ($mail) use ($email, $subject) {
            $mail->to($email)
                 ->subject($subject);
        });

    } catch (\Exception $e) {
        \Log::error('Failed to send contact response email: ' . $e->getMessage());
    }
}
}
