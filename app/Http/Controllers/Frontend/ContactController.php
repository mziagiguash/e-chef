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

        // Определяем отправителя
        $senderId = null;
        $senderType = null;
        $user = Auth::user();

        if ($user) {
            // Если пользователь - студент
            if ($user->student_id) {
                $senderId = $user->student_id;
                $senderType = 'student';
                $student = Student::find($user->student_id);

                // Автозаполняем если поля пустые
                if (empty($validated['name']) && $student) {
                    $validated['name'] = $student->name;
                }
                if (empty($validated['email']) && $student) {
                    $validated['email'] = $student->email;
                }
            }
            // Если пользователь - инструктор
            elseif ($user->instructor_id) {
                $senderId = $user->instructor_id;
                $senderType = 'instructor';
                $instructor = Instructor::find($user->instructor_id);

                if (empty($validated['name']) && $instructor) {
                    $validated['name'] = $instructor->name;
                }
                if (empty($validated['email']) && $instructor) {
                    $validated['email'] = $instructor->email;
                }
            }
            // Обычный пользователь (админ и т.д.)
            else {
                if (empty($validated['name'])) {
                    $validated['name'] = $user->name;
                }
                if (empty($validated['email'])) {
                    $validated['email'] = $user->email;
                }
            }
        }

        // Сохраняем сообщение
        ContactMessage::create([
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        return redirect()->route('contact', ['locale' => app()->getLocale()])->with([
            'success' => 'Your message has been sent successfully! We will get back to you soon.'
        ]);
    }
}
