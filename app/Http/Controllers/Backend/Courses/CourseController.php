<?php

namespace App\Http\Controllers\Backend\Courses;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\Course\Courses\AddNewRequest;
use App\Http\Requests\Backend\Course\Courses\UpdateRequest;
use App\Models\CourseCategory;
use App\Models\Instructor;
use Illuminate\Support\Facades\DB;
use Exception;

class CourseController extends Controller
{
    /**
     * Вспомогательные методы для локализации
     */
    private function localizeCategories($categories)
    {
        return $categories->map(function ($item) {
            $names = is_string($item->category_name) ? json_decode($item->category_name, true) : (array)$item->category_name;
            $item->display_name = $names[app()->getLocale()] ?? reset($names) ?? 'No Name';
            return $item;
        });
    }

    private function localizeInstructors($instructors)
    {
        return $instructors->map(function ($i) {
            $names = is_string($i->name) ? json_decode($i->name, true) : (array)$i->name;
            $i->display_name = $names[app()->getLocale()] ?? reset($names) ?? '';
            return $i;
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $course = Course::paginate(10);
        return view('backend.course.courses.index', compact('course'));
    }

public function indexForAdmin(Request $request)
{
    // Определяем текущую локаль: из запроса или по умолчанию
    $locale = $request->query('locale', app()->getLocale());

    // Устанавливаем локаль приложения
    app()->setLocale($locale);

    // Получаем курсы с пагинацией
    $courses = Course::with(['translations', 'instructor.translations', 'courseCategory.translations'])
                     ->paginate(10);

    return view('backend.course.courses.indexForAdmin', compact('courses', 'locale'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courseCategory = $this->localizeCategories(CourseCategory::all());
        $instructors = $this->localizeInstructors(Instructor::all());

        return view('backend.course.courses.create', compact('courseCategory', 'instructors'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit($id)
    {
        $courseCategory = $this->localizeCategories(CourseCategory::all());
        $instructors = $this->localizeInstructors(Instructor::all());
        $course = Course::findOrFail(encryptor('decrypt', $id));

        return view('backend.course.courses.edit', compact('courseCategory', 'instructors', 'course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddNewRequest $request)
    {
        try {
            DB::beginTransaction();

            $course = new Course();
            $course->course_category_id = $request->course_category_id;
            $course->instructor_id = $request->instructor_id;
            $course->type = $request->courseType;
            $course->price = $request->coursePrice;
            $course->old_price = $request->courseOldPrice;
            $course->subscription_price = $request->subscription_price;
            $course->start_from = $request->start_from;
            $course->duration = $request->duration;
            $course->lesson = $request->lesson;
            $course->difficulty = $request->courseDifficulty;
            $course->course_code = $request->course_code;
            $course->tag = $request->tag;
            $course->status = 2; // активен по умолчанию
            $course->language = app()->getLocale();

            // Видео
            if ($request->hasFile('thumbnail_video_file')) {
                $course->thumbnail_video = $request->file('thumbnail_video_file')->store('uploads/videos', 'public');
            } elseif ($request->filled('thumbnail_video_url')) {
                $course->thumbnail_video = $request->thumbnail_video_url;
            }

            // Изображения
            if ($request->hasFile('image')) {
                $imageName = rand(111, 999) . time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/courses'), $imageName);
                $course->image = $imageName;
            }

            if ($request->hasFile('thumbnail_image')) {
                $thumbName = rand(111, 999) . time() . '.' . $request->thumbnail_image->extension();
                $request->thumbnail_image->move(public_path('uploads/courses/thumbnails'), $thumbName);
                $course->thumbnail_image = $thumbName;
            }

            $course->save();

            // Переводы
            $translations = $request->input('translations', []);
            foreach ($translations as $locale => $fields) {
                $course->translations()->create([
                    'locale' => $locale,
                    'title' => $fields['title'] ?? '',
                    'description' => $fields['description'] ?? '',
                    'prerequisites' => $fields['prerequisites'] ?? '',
                    'keywords' => $fields['keywords'] ?? '',
                ]);
            }

            DB::commit();
            return redirect()->route('course.index')->with('success', 'Course created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $course = Course::findOrFail(encryptor('decrypt', $id));

            $course->course_category_id = $request->course_category_id;
            $course->instructor_id = $request->instructor_id;
            $course->type = $request->courseType;
            $course->price = $request->coursePrice;
            $course->old_price = $request->courseOldPrice;
            $course->subscription_price = $request->subscription_price;
            $course->start_from = $request->start_from;
            $course->duration = $request->duration;
            $course->lesson = $request->lesson;
            $course->difficulty = $request->courseDifficulty;
            $course->course_code = $request->course_code;
            $course->tag = $request->tag;
            $course->status = $request->status ?? $course->status;

            // Видео
            if ($request->hasFile('thumbnail_video_file')) {
                $course->thumbnail_video = $request->file('thumbnail_video_file')->store('uploads/videos', 'public');
            } elseif ($request->filled('thumbnail_video_url')) {
                $course->thumbnail_video = $request->thumbnail_video_url;
            }

            // Изображения
            if ($request->hasFile('image')) {
                $imageName = rand(111, 999) . time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/courses'), $imageName);
                $course->image = $imageName;
            }

            if ($request->hasFile('thumbnail_image')) {
                $thumbName = rand(111, 999) . time() . '.' . $request->thumbnail_image->extension();
                $request->thumbnail_image->move(public_path('uploads/courses/thumbnails'), $thumbName);
                $course->thumbnail_image = $thumbName;
            }

            $course->save();

            // Обновляем переводы
            $course->translations()->delete();
            $translations = $request->input('translations', []);
            foreach ($translations as $locale => $fields) {
                $course->translations()->create([
                    'locale' => $locale,
                    'title' => $fields['title'] ?? '',
                    'description' => $fields['description'] ?? '',
                    'prerequisites' => $fields['prerequisites'] ?? '',
                    'keywords' => $fields['keywords'] ?? '',
                ]);
            }

            DB::commit();
            return redirect()->route('course.index')->with('success', 'Course updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Please try again');
        }
    }

    /**
     * Update for Admin.
     */
    public function updateForAdmin(UpdateRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $course = Course::findOrFail(encryptor('decrypt', $id));

            $course->course_category_id = $request->course_category_id;
            $course->instructor_id = $request->instructor_id;
            $course->type = $request->courseType;
            $course->price = $request->coursePrice;
            $course->old_price = $request->courseOldPrice;
            $course->subscription_price = $request->subscription_price;
            $course->start_from = $request->start_from;
            $course->duration = $request->duration;
            $course->lesson = $request->lesson;
            $course->difficulty = $request->courseDifficulty;
            $course->course_code = $request->course_code;
            $course->tag = $request->tag;
            $course->status = $request->status;
            $course->language = 'en';

            // Видео
            if ($request->hasFile('thumbnail_video_file')) {
                $course->thumbnail_video = $request->file('thumbnail_video_file')->store('uploads/videos', 'public');
            } elseif ($request->filled('thumbnail_video_url')) {
                $course->thumbnail_video = $request->thumbnail_video_url;
            }

            // Изображения
            if ($request->hasFile('image')) {
                $imageName = rand(111, 999) . time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/courses'), $imageName);
                $course->image = $imageName;
            }

            if ($request->hasFile('thumbnail_image')) {
                $thumbName = rand(111, 999) . time() . '.' . $request->thumbnail_image->extension();
                $request->thumbnail_image->move(public_path('uploads/courses/thumbnails'), $thumbName);
                $course->thumbnail_image = $thumbName;
            }

            $course->save();

            // Обновляем переводы
            $course->translations()->delete();
            $translations = $request->input('translations', []);
            foreach ($translations as $locale => $fields) {
                $course->translations()->create([
                    'locale' => $locale,
                    'title' => $fields['title'] ?? '',
                    'description' => $fields['description'] ?? '',
                    'prerequisites' => $fields['prerequisites'] ?? '',
                    'keywords' => $fields['keywords'] ?? '',
                ]);
            }

            DB::commit();
            return redirect()->route('courseList')->with('success', 'Data Saved');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Please try again');
        }
    }
    public function frontShow($id)
{
    $course = Course::findOrFail($id);
    return view('frontend.courseDetails', compact('course'));
}

}
