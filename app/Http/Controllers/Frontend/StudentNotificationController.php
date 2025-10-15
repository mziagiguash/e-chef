<?php
// app/Http\Controllers\Frontend\StudentNotificationController.php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentNotificationController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $notifications = Notification::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unread_count = Notification::where('student_id', $student->id)
            ->unread()
            ->count();

        return view('frontend.student.notifications', compact('notifications', 'unread_count'));
    }

    public function markAsRead($id)
    {
        $student = Auth::guard('student')->user();
        $notification = Notification::where('student_id', $student->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $student = Auth::guard('student')->user();
        Notification::where('student_id', $student->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
