<?php
// app/Http\Controllers\Frontend\ContactController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:500',
            'message' => 'required|string|min:10|max:5000',
        ]);

        \Log::info('=== CONTACT FORM SUBMITTED ===', [
            'form_data' => $validated,
            'student_authenticated' => Auth::guard('student')->check(),
            'instructor_authenticated' => Auth::guard('instructor')->check(),
            'user_authenticated' => Auth::check()
        ]);

        // Определяем отправителя
        $senderId = null;
        $senderType = null;

        // Проверяем студента
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();
            $senderId = $student->id;
            $senderType = 'student';

            \Log::info('Student authenticated for contact form', [
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);

            // Автозаполняем имя и email если не заполнены
            if (empty($validated['name'])) {
                $validated['name'] = $student->name;
            }
            if (empty($validated['email'])) {
                $validated['email'] = $student->email;
            }
        }
        // Проверяем инструктора
        elseif (Auth::guard('instructor')->check()) {
            $instructor = Auth::guard('instructor')->user();
            $senderId = $instructor->id;
            $senderType = 'instructor';

            \Log::info('Instructor authenticated for contact form', [
                'instructor_id' => $instructor->id,
                'instructor_name' => $instructor->name
            ]);

            if (empty($validated['name'])) {
                $validated['name'] = $instructor->name;
            }
            if (empty($validated['email'])) {
                $validated['email'] = $instructor->email;
            }
        }
        // Проверяем обычного пользователя (админ и т.д.)
        elseif (Auth::check()) {
            $user = Auth::user();
            $senderType = 'user';

            \Log::info('User authenticated for contact form', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);

            if (empty($validated['name'])) {
                $validated['name'] = $user->name;
            }
            if (empty($validated['email'])) {
                $validated['email'] = $user->email;
            }
        }
        else {
            \Log::info('Guest user submitted contact form');
        }

        \Log::info('Creating contact message with sender info:', [
            'sender_id' => $senderId,
            'sender_type' => $senderType
        ]);

        // Создаем сообщение
        $contactMessage = ContactMessage::create([
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        \Log::info('✅ CONTACT MESSAGE CREATED', [
            'contact_message_id' => $contactMessage->id,
            'sender_type' => $contactMessage->sender_type,
            'sender_id' => $contactMessage->sender_id
        ]);

        return redirect()->route('contact', ['locale' => app()->getLocale()])->with('success', 'Your message has been sent successfully!');
    }
}
