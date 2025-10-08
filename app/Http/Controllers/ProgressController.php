<?php
// app/Http/Controllers/ProgressController.php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\Student;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function updateProgress(Request $request, $courseId)
    {
        $user = auth()->user();
        $progress = $request->input('progress', 0);
        $completed = $request->input('completed', false);

        $courseProgress = Progress::updateOrCreate(
            [
                'student_id' => $user->id,
                'course_id' => $courseId
            ],
            [
                'progress_percentage' => $progress,
                'completed' => $completed,
                'last_viewed_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $courseProgress->progress_percentage,
            'completed' => $courseProgress->completed
        ]);
    }

    public function markAsCompleted($courseId)
    {
        $user = auth()->user();

        // Получаем студента (предполагая, что User и Student связаны)
        $student = Student::find($user->id);

        // Если у вас отдельная модель Student с внешним ключом к User
        // $student = Student::where('user_id', $user->id)->first();

        $progress = Progress::updateOrCreate(
            [
                'student_id' => $user->id,
                'course_id' => $courseId
            ],
            [
                'progress_percentage' => 100,
                'completed' => true,
                'last_viewed_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Course marked as completed!',
            'can_review' => $student->canReviewCourse($courseId)
        ]);
    }
}
