<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonTranslation;
use App\Models\Quiz;
use App\Models\QuizTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with([
            'translations', // ← обязательно добавить
            'course.translations', // ← если нужно название курса на разных языках
            'materials', // ← для материалов
            'quiz.translations' // ← если нужны переводы квиза
        ])->latest()->paginate(20);

        return view('backend.course.lesson.index', compact('lessons'));
    }

    public function create()
    {
        $courses = Course::with(['translations' => function($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        return view('backend.course.lesson.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.notes' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'video' => 'nullable|file|mimes:mp4,avi,mov|max:102400', // 100MB max
            'video_url' => 'nullable|url',
            'order' => 'required|integer|min:1',
            'has_quiz' => 'nullable|boolean',
            'quiz_translations.*.title' => 'nullable|required_with:has_quiz|string|max:255',
            'quiz_translations.*.description' => 'nullable|string',
            'passing_score' => 'nullable|integer|min:1|max:100'
        ]);

        DB::beginTransaction();

        try {
            // Create lesson
            $lesson = new Lesson();
            $lesson->course_id = $request->course_id;
            $lesson->order = $request->order;

            // Handle video upload
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('lessons/videos', 'public');
                $lesson->video_url = $videoPath;
            } elseif ($request->video_url) {
                $lesson->video_url = $request->video_url;
            }

            $lesson->save();

            // Save translations
            foreach ($request->translations as $locale => $translationData) {
                if (!empty($translationData['title'])) {
                    $lessonTranslation = new LessonTranslation();
                    $lessonTranslation->lesson_id = $lesson->id;
                    $lessonTranslation->locale = $locale;
                    $lessonTranslation->title = $translationData['title'];
                    $lessonTranslation->description = $translationData['description'] ?? null;
                    $lessonTranslation->notes = $translationData['notes'] ?? null;
                    $lessonTranslation->save();
                }
            }

            // Create quiz if requested
            if ($request->has_quiz) {
                $quiz = new Quiz();
                $quiz->lesson_id = $lesson->id;
                $quiz->passing_score = $request->passing_score ?? 70;
                $quiz->save();

                // Save quiz translations
                foreach ($request->quiz_translations as $locale => $quizData) {
                    if (!empty($quizData['title'])) {
                        $quizTranslation = new QuizTranslation();
                        $quizTranslation->quiz_id = $quiz->id;
                        $quizTranslation->locale = $locale;
                        $quizTranslation->title = $quizData['title'];
                        $quizTranslation->description = $quizData['description'] ?? null;
                        $quizTranslation->save();
                    }
                }
            }

            DB::commit();

            return redirect(localeRoute('lesson.index'))
                ->with('success', 'Lesson created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating lesson: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $lesson = Lesson::with(['translations', 'quiz.translations'])->findOrFail(encryptor('decrypt', $id));
        $courses = Course::with(['translations' => function($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        return view('backend.course.lesson.edit', compact('lesson', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

        $request->validate([
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
            'translations.*.notes' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'video' => 'nullable|file|mimes:mp4,avi,mov|max:102400', // 100MB max
            'video_url' => 'nullable|url',
            'order' => 'required|integer|min:1',
            'has_quiz' => 'nullable|boolean',
            'quiz_translations.*.title' => 'nullable|required_with:has_quiz|string|max:255',
            'quiz_translations.*.description' => 'nullable|string',
            'passing_score' => 'nullable|integer|min:1|max:100'
        ]);

        DB::beginTransaction();

        try {
            // Update lesson
            $lesson->course_id = $request->course_id;
            $lesson->order = $request->order;

            // Handle video upload/update
            if ($request->hasFile('video')) {
                // Delete old video if exists
                if ($lesson->video_url && !str_contains($lesson->video_url, 'youtube.com') && !str_contains($lesson->video_url, 'youtu.be')) {
                    Storage::disk('public')->delete($lesson->video_url);
                }

                $videoPath = $request->file('video')->store('lessons/videos', 'public');
                $lesson->video_url = $videoPath;
            } elseif ($request->video_url) {
                // Delete old uploaded video if switching to YouTube
                if ($lesson->video_url && !str_contains($lesson->video_url, 'youtube.com') && !str_contains($lesson->video_url, 'youtu.be')) {
                    Storage::disk('public')->delete($lesson->video_url);
                }
                $lesson->video_url = $request->video_url;
            }

            $lesson->save();

            // Update translations
            foreach ($request->translations as $locale => $translationData) {
                $translation = LessonTranslation::updateOrCreate(
                    [
                        'lesson_id' => $lesson->id,
                        'locale' => $locale
                    ],
                    [
                        'title' => $translationData['title'],
                        'description' => $translationData['description'] ?? null,
                        'notes' => $translationData['notes'] ?? null
                    ]
                );
            }

            // Handle quiz
            if ($request->has_quiz) {
                $quiz = $lesson->quiz ?? new Quiz();
                $quiz->lesson_id = $lesson->id;
                $quiz->passing_score = $request->passing_score ?? 70;
                $quiz->save();

                // Update quiz translations
                foreach ($request->quiz_translations as $locale => $quizData) {
                    if (!empty($quizData['title'])) {
                        QuizTranslation::updateOrCreate(
                            [
                                'quiz_id' => $quiz->id,
                                'locale' => $locale
                            ],
                            [
                                'title' => $quizData['title'],
                                'description' => $quizData['description'] ?? null
                            ]
                        );
                    }
                }
            } elseif ($lesson->quiz) {
                // Delete quiz if it exists but not requested
                $lesson->quiz->delete();
            }

            DB::commit();

            return redirect(localeRoute('lesson.index'))
                ->with('success', 'Lesson updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating lesson: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $lesson = Lesson::findOrFail(encryptor('decrypt', $id));

        DB::beginTransaction();
        try {
            // Delete video file if exists
            if ($lesson->video_url && !str_contains($lesson->video_url, 'youtube.com') && !str_contains($lesson->video_url, 'youtu.be')) {
                Storage::disk('public')->delete($lesson->video_url);
            }

            $lesson->delete();

            DB::commit();

            return redirect(localeRoute('lesson.index'))
                ->with('success', 'Lesson deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting lesson: ' . $e->getMessage());
        }
    }
}
