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
public function index()
{
    $contactMessages = ContactMessage::orderBy('created_at', 'desc')->paginate(10);

    // Передаём статистику в view
    $stats = [
        'new' => ContactMessage::where('status', 'new')->count(),
        'in_progress' => ContactMessage::where('status', 'in_progress')->count(),
        'resolved' => ContactMessage::where('status', 'resolved')->count(),
    ];

    return view('backend.communication.contact-message.index', compact('contactMessages', 'stats'));
}

    public function show($id)
    {
        $contactMessage = ContactMessage::findOrFail($id);

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
    $request->validate([
        'response_subject' => 'required|string|max:255',
        'response_message' => 'required|string|min:10|max:5000',
        'also_send_email' => 'nullable|boolean'
    ]);

    $contactMessage = ContactMessage::findOrFail($id);

    // Сохраняем ответ как admin_notes
    $contactMessage->update([
        'admin_notes' => $request->response_message,
        'status' => 'resolved',
        'resolved_at' => $contactMessage->resolved_at ?? now()
    ]);

    // СОЗДАЁМ УВЕДОМЛЕНИЕ ДЛЯ СТУДЕНТА
    $notification = NotificationService::contactMessageReplied($contactMessage);

    // ВРЕМЕННО ОТКЛЮЧАЕМ EMAIL ИЗ-ЗА ПРОБЛЕМ С КОНФИГУРАЦИЕЙ
    // if ($request->also_send_email) {
    //     $this->sendResponseEmail($contactMessage, $request->response_subject, $request->response_message);
    // }

    return redirect()->back()->with('success', 'Response sent to student successfully! Student will see it in notifications.');
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
