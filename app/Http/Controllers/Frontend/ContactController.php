<?php
// app/Http\Controllers\Frontend\ContactController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // 🔴 ДОБАВЛЕНО: Вспомогательный метод для получения student_id
    private function getStudentId()
    {
        return session('userId') ? encryptor('decrypt', session('userId')) : null;
    }

    // 🔴 ДОБАВЛЕНО: Вспомогательный метод для проверки аутентификации студента
    private function checkStudentAuth()
    {
        $student_id = $this->getStudentId();

        if (!$student_id) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login as student'
                ], 401);
            }
            return redirect()->route('studentLogin', ['locale' => app()->getLocale()])
                           ->with('error', 'Please login as student');
        }

        $student_info = Student::find($student_id);

        if (!$student_info) {
            session()->flush();
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 401);
            }
            return redirect()->route('studentLogin', ['locale' => app()->getLocale()])
                           ->with('error', 'Student not found');
        }

        return $student_info;
    }

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
            'has_student_session' => session()->has('userId')
        ]);

        // Определяем отправителя
        $senderId = null;
        $senderType = null;

        // 🔴 ИСПРАВЛЕНО: Проверяем студента через session
        $student_id = $this->getStudentId();
        if ($student_id) {
            $student = Student::find($student_id);
            if ($student) {
                $senderId = $student_id;
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
        }
        // Проверяем обычного пользователя (админ и т.д.)
        elseif (Auth::check()) {
            $user = Auth::user();
            $senderId = $user->id;
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

    public function myMessages()
    {
        // 🔴 ИСПРАВЛЕНО: Используем проверку студента
        $studentAuth = $this->checkStudentAuth();
        if (!$studentAuth instanceof Student) {
            return $studentAuth; // Возвращаем редирект или JSON ошибку
        }

        $student_id = $studentAuth->id;

        try {
            // 🔴 ИСПРАВЛЕННЫЙ ЗАПРОС: Получаем сообщения студента
            $messages = ContactMessage::where(function($query) use ($student_id) {
                    $query->where('sender_id', $student_id)
                          ->where('sender_type', 'student')
                          ->whereNull('parent_id'); // Только родительские сообщения
                })
                ->orWhere(function($query) use ($student_id) {
                    $query->where('sender_id', $student_id)
                          ->where('sender_type', 'student')
                          ->whereNotNull('parent_id'); // Или ответы студента
                })
                ->orWhere(function($query) use ($student_id) {
                    $query->whereNotNull('parent_id')
                          ->whereIn('parent_id', function($subquery) use ($student_id) {
                              $subquery->select('id')
                                       ->from('contact_messages')
                                       ->where('sender_id', $student_id)
                                       ->where('sender_type', 'student')
                                       ->whereNull('parent_id');
                          });
                })
                ->with(['replies' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            // 🔴 УПРОЩЕННАЯ ГРУППИРОВКА: Группируем по parent_id или id
            $groupedMessages = $messages->groupBy(function($message) {
                return $message->parent_id ?: $message->id;
            });

            \Log::info('✅ MY MESSAGES LOADED', [
                'student_id' => $student_id,
                'total_messages' => $messages->count(),
                'conversations_count' => $groupedMessages->count()
            ]);

            return view('students.my-messages', ['messages' => $groupedMessages]);

        } catch (\Exception $e) {
            \Log::error('❌ ERROR LOADING MESSAGES: ' . $e->getMessage());

            return view('students.my-messages', ['messages' => collect()])
                ->with('error', 'Error loading messages: ' . $e->getMessage());
        }
    }

    public function deleteMessage($id)
    {
        // 🔴 ИСПРАВЛЕНО: Используем проверку студента
        $studentAuth = $this->checkStudentAuth();
        if (!$studentAuth instanceof Student) {
            return $studentAuth; // Возвращаем редирект или JSON ошибку
        }

        $student_id = $studentAuth->id;

        try {
            $message = ContactMessage::findOrFail($id);

            // 🔴 ИСПРАВЛЕНО: Проверяем, что сообщение принадлежит студенту
            if ($message->sender_id !== $student_id || $message->sender_type !== 'student') {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to delete this message'
                    ], 403);
                }
                return redirect()->back()->with('error', 'You are not authorized to delete this message');
            }

            // Нельзя удалять сообщения, на которые есть ответы
            if ($message->replies()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete message that has replies'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete message that has replies');
            }

            $message->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message deleted successfully'
                ]);
            }

            return redirect()->route('student.my-messages')->with('success', 'Message deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting message: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting message: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error deleting message: ' . $e->getMessage());
        }
    }

    // 🔴 ОБНОВЛЕНО: Метод для продолжения диалога с поддержкой session студента
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

            // 🔴 ИСПРАВЛЕНО: Проверяем, что студент имеет доступ к этому диалогу
            $isOwner = $parentMessage->sender_id === $student_id && $parentMessage->sender_type === 'student';
            $isInConversation = ContactMessage::where('id', $parentMessage->parent_id ?: $parentMessage->id)
                ->where('sender_id', $student_id)
                ->where('sender_type', 'student')
                ->exists();

            if (!$isOwner && !$isInConversation) {
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
                'parent_id' => $parentMessage->id,
                'name' => $studentAuth->name,
                'email' => $studentAuth->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'in_progress'
            ]);

            // Обновляем статус родительского сообщения на in_progress
            $parentMessage->update([
                'status' => 'in_progress',
                'resolved_at' => null
            ]);

            // 🔴 ДОБАВЛЕНО: Создаем уведомление для админа о новом ответе
            // Здесь можно добавить отправку уведомления администратору

            \Log::info('✅ CONVERSATION CONTINUED BY STUDENT', [
                'parent_message_id' => $parentMessage->id,
                'new_message_id' => $newMessage->id,
                'student_id' => $student_id,
                'student_name' => $studentAuth->name
            ]);

            // 🔴 ВАЖНО: Поддержка AJAX и обычных запросов
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your reply has been sent successfully! The conversation has been reopened.'
                ]);
            }

            return redirect()->back()->with('success', 'Your reply has been sent successfully!');

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

    // 🔴 УДАЛЕН: Старый метод getSenderType(), так как теперь используем только студента
}
