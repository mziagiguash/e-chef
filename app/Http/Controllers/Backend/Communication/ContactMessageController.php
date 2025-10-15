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
    // 🔴 ИСПРАВЛЕНО: Без отношений в основном запросе
    $query = ContactMessage::orderBy('created_at', 'desc');

    if ($request->has('status') && in_array($request->status, ['new', 'in_progress', 'resolved'])) {
        $query->where('status', $request->status);
    }

    $contactMessages = $query->paginate(10);

    // 🔴 ДОБАВЛЕНО: Загружаем отношения отдельно для каждого сообщения
    $contactMessages->getCollection()->transform(function ($message) {
        if ($message->sender_type === 'student') {
            $message->load('student');
        } elseif ($message->sender_type === 'instructor') {
            $message->load('instructor');
        }
        return $message;
    });

    $stats = [
        'new' => ContactMessage::where('status', 'new')->count(),
        'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
        'resolved' => ContactMessage::where('status', 'resolved')->count(),
        'total' => ContactMessage::count(),
    ];

    return view('backend.communication.contact-message.index', compact('contactMessages', 'stats'));
}

public function show($id)
{
    $contactMessage = ContactMessage::findOrFail($id);

    // 🔴 ИСПРАВЛЕНО: Загружаем отношения безопасно
    if ($contactMessage->sender_type === 'student') {
        $contactMessage->load('student');
    } elseif ($contactMessage->sender_type === 'instructor') {
        $contactMessage->load('instructor');
    }

    if ($contactMessage->status == 'new') {
        $contactMessage->update(['status' => 'in_progress']);
    }

    return view('backend.communication.contact-message.show', compact('contactMessage'));
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contactMessage = ContactMessage::findOrFail($id);
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
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
