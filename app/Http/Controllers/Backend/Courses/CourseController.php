<?php

namespace App\Http\Controllers\Backend\Courses;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Instructor;
use App\Models\CourseTranslation;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\Course\Courses\AddNewRequest;
use App\Http\Requests\Backend\Course\Courses\UpdateRequest;
use Exception;
use File;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $query = Course::with(['translations', 'instructor.translations', 'courseCategory.translations', 'lessons'])
            ->withCount('lessons');



        $courses = $query->paginate(10);

        return view('backend.course.courses.index', compact('courses'));
    }

    public function indexForAdmin(Request $request)
    {
        $query = Course::with(['translations', 'instructor.translations', 'courseCategory.translations', 'lessons'])
            ->withCount('lessons');

        // Поиск по названию курса
        if ($request->has('search_title') && !empty($request->search_title)) {
            $query->whereHas('translations', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search_title . '%');
            });
        }

        // Поиск по инструктору
        if ($request->has('search_instructor') && !empty($request->search_instructor)) {
            $query->whereHas('instructor.translations', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_instructor . '%');
            });
        }

        $courses = $query->paginate(10);

        return view('backend.course.courses.indexForAdmin', compact('courses'));
    }

    public function create()
    {
        $courseCategory = CourseCategory::with('translations')->get();
        $instructors = Instructor::with('translations')->get();
        $locales = config('app.available_locales', ['en', 'ru', 'ka']);
        return view('backend.course.courses.create', compact('courseCategory', 'instructors', 'locales'));
    }

    public function store(AddNewRequest $request)
    {
        return $this->saveCourse(new Course(), $request);
    }

public function show($id)
{
    $course = Course::with([
            'translations',
            'instructor.translations',
            'courseCategory.translations',
            'lessons'
        ])
        ->withCount('lessons')
        ->findOrFail(encryptor('decrypt', $id));

    return view('backend.course.courses.show', compact('course'));
}

    public function edit($id)
    {
        $course = Course::with('translations')->findOrFail(encryptor('decrypt', $id));
        $courseCategory = CourseCategory::with('translations')->get();
        $instructors = Instructor::with('translations')->get();
        $locales = config('app.available_locales', ['en', 'ru', 'ka']);
        return view('backend.course.courses.edit', compact('course', 'courseCategory', 'instructors', 'locales'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $course = Course::findOrFail(encryptor('decrypt', $id));
        return $this->saveCourse($course, $request);
    }

    protected function saveCourse(Course $course, Request $request)
    {
        DB::beginTransaction();
        try {
            // Основные поля
            $course->course_category_id = $request->course_category_id;
            $course->instructor_id = $request->instructor_id;
            $course->courseType = $request->courseType ?? $course->courseType ?? 'free';
            $course->coursePrice = $request->coursePrice ?? $course->coursePrice ?? 0;
            $course->courseOldPrice = $request->courseOldPrice ?? $course->courseOldPrice ?? 0;
            $course->subscription_price = $request->subscription_price ?? $course->subscription_price ?? 0;
            $course->start_from = $request->start_from;
            $course->duration = $request->duration;
            $course->lesson = $request->lesson;
            $course->course_code = $request->course_code;
            $course->tag = $request->tag;
            $course->status = is_numeric($request->input('status')) ? (int) $request->input('status') : 2;

            // Видео
            if ($request->hasFile('thumbnail_video_file')) {
                $course->thumbnail_video_file = $request->file('thumbnail_video_file')->store('uploads/videos', 'public');
            } elseif ($request->filled('thumbnail_video_url')) {
                $course->thumbnail_video_url = $request->thumbnail_video_url;
            }

            // Изображения
            if ($request->hasFile('image')) {
                if ($course->image && File::exists(public_path('uploads/courses/'.$course->image))) {
                    File::delete(public_path('uploads/courses/'.$course->image));
                }
                $imageName = rand(111, 999) . time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/courses'), $imageName);
                $course->image = $imageName;
            }

            if ($request->hasFile('thumbnail_image')) {
                if ($course->thumbnail_image && File::exists(public_path('uploads/courses/'.$course->thumbnail_image))) {
                    File::delete(public_path('uploads/courses/'.$course->thumbnail_image));
                }
                $thumbName = rand(111, 999) . time() . '.' . $request->thumbnail_image->extension();
                $request->thumbnail_image->move(public_path('uploads/courses'), $thumbName);
                $course->thumbnail_image = $thumbName;
            }

            $course->save();

            // Сохраняем переводы
            if ($request->has('translations')) {
                foreach ($request->translations as $locale => $translationData) {
                    if (isset($translationData['id'])) {
                        $translation = CourseTranslation::find($translationData['id']);
                        if ($translation) {
                            $translation->update([
                                'title' => $translationData['title'] ?? '',
                                'description' => $translationData['description'] ?? '',
                                'prerequisites' => $translationData['prerequisites'] ?? '',
                                'keywords' => $translationData['keywords'] ?? '',
                            ]);
                        }
                    } else {
                        $course->translations()->create([
                            'locale' => $locale,
                            'title' => $translationData['title'] ?? '',
                            'description' => $translationData['description'] ?? '',
                            'prerequisites' => $translationData['prerequisites'] ?? '',
                            'keywords' => $translationData['keywords'] ?? '',
                        ]);
                    }
                }
            }

            DB::commit();

            // Всегда редиректим на админский маршрут
            return redirect()->route('courses.index')->with('success', 'Course saved successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $course = Course::findOrFail(encryptor('decrypt', $id));

            // Удаляем изображения
            if ($course->image && File::exists(public_path('uploads/courses/'.$course->image))) {
                File::delete(public_path('uploads/courses/'.$course->image));
            }
            if ($course->thumbnail_image && File::exists(public_path('uploads/courses/thumbnails/'.$course->thumbnail_image))) {
                File::delete(public_path('uploads/courses/thumbnails/'.$course->thumbnail_image));
            }

            // Удаляем переводы
            $course->translations()->delete();

            $course->delete();

            DB::commit();
            return redirect()->route('admin.courses')->with('success', 'Course deleted successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.courses')->with('error', 'Error: '.$e->getMessage());
        }
    }
}
