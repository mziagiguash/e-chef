<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('course')->paginate(10);

        $locale = app()->getLocale();
        foreach ($lessons as $lesson) {
            $lesson->display_title = $lesson->displayTitle($locale);
            $lesson->display_course = $lesson->course ? $lesson->course->getTranslation('title', $locale) : 'No Course';
        }

        return view('backend.course.lesson.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('backend.course.lesson.create', compact('courses'));
    }

   public function store(Request $request)
{
    $request->validate([
        'lessonTitle' => 'required|array',
        'lessonTitle.*' => 'required|string|max:255',
        'courseId' => 'required|exists:courses,id',
        'lessonDescription' => 'nullable|array',
        'lessonNotes' => 'nullable|array',
    ]);

    try {
        Lesson::create([
            'title' => $request->lessonTitle,
            'course_id' => $request->courseId,
            'description' => $request->lessonDescription ?? [],
            'notes' => $request->lessonNotes ?? [],
        ]);

        $this->notice::success('Data Saved');
        return redirect()->route('lesson.index');
    } catch (\Exception $e) {
        $this->notice::error('Please try again');
        return redirect()->back()->withInput();
    }
}
public function edit($id)
{
    $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

    // Приводим массивы к пустым массивам, если вдруг NULL
    $lesson->title = (array) $lesson->title;
    $lesson->description = (array) $lesson->description;
    $lesson->notes = (array) $lesson->notes;

    $courses = Course::all();

    return view('backend.course.lesson.edit', compact('lesson', 'courses'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'lessonTitle' => 'required|array',
        'lessonTitle.*' => 'required|string|max:255',
        'courseId' => 'required|exists:courses,id',
        'lessonDescription' => 'nullable|array',
        'lessonNotes' => 'nullable|array',
    ]);

    try {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

        $lesson->update([
            'title' => $request->lessonTitle,
            'course_id' => $request->courseId,
            'description' => $request->lessonDescription ?? [],
            'notes' => $request->lessonNotes ?? [],
        ]);

        $this->notice::success('Data Saved');
        return redirect()->route('lesson.index');
    } catch (\Exception $e) {
        $this->notice::error('Please try again');
        return redirect()->back()->withInput();
    }
}

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));
        $lesson->delete();

        $this->notice::success('Data Deleted!');
        return redirect()->back();
    }
}
