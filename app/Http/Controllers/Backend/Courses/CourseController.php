<?php

namespace App\Http\Controllers\Backend\Courses;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\Course\Courses\AddNewRequest;
use App\Http\Requests\Backend\Course\Courses\UpdateRequest;
use App\Models\CourseCategory;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

use Exception;
use File;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $course = Course::paginate(10);
        return view('backend.course.courses.index', compact('course'));
    }

    public function indexForAdmin()
    {
        $course = Course::paginate(10);
        return view('backend.course.courses.indexForAdmin', compact('course'));
    }

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $courseCategory = CourseCategory::all()->map(function ($item) {
    $names = json_decode($item->category_name, true);

    if (is_array($names) && count($names) > 0) {
        // Если есть перевод на текущий язык — используем, иначе первый элемент массива
        $item->category_name = $names[app()->getLocale()] ?? reset($names);
    } else {
        // Если json_decode вернул null или пустой массив
        $item->category_name = $item->category_name ?: 'No Name';
    }

    return $item;
});


    $instructor = Instructor::all();
    return view('backend.course.courses.create', compact('courseCategory', 'instructor'));
}

    /**
     * Store a newly created resource in storage.
     */
public function store(AddNewRequest $request)
{

    try {
        DB::beginTransaction();

        $course = new Course();
        $course->course_category_id = $request->categoryId;
        $course->instructor_id = $request->instructorId;
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

        if ($request->hasFile('thumbnail_video_file')) {
            $videoPath = $request->file('thumbnail_video_file')->store('uploads/videos', 'public');
            $course->thumbnail_video = $videoPath;
        } elseif ($request->filled('thumbnail_video_url')) {
            $course->thumbnail_video = $request->input('thumbnail_video_url');
        }

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

        // Сохраняем переводы
        $translations = $request->input('translations', []);
        foreach ($translations as $locale => $fields) {
            $course->translations()->create([
            'locale' => $locale,
            'title' => $fields['title'],
            'description' => $fields['description'] ?? null,
            'prerequisites' => $fields['prerequisites'] ?? null,
            ]);
        }

        DB::commit();

        return redirect()->route('course.index')->with('success', 'Course created successfully.');

    } catch (\Exception $e) {
    DB::rollBack();

    // Временно выводим подробности ошибки:
    return redirect()->back()
        ->withInput()
        ->with('error', 'Ошибка: ' . $e->getMessage() . ' в файле ' . $e->getFile() . ' на строке ' . $e->getLine());
}

}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

public function frontShow($id)
{
    $course = Course::findOrFail(encryptor('decrypt', $id));

    // Получаем валюту и курс из сессии, или задаём значения по умолчанию
    $currencySymbol = session('currency_symbol', '$');
    $currencyRate = session('currency_rate', 1.0);

    return view('frontend.courseDetails', compact('course', 'currencySymbol', 'currencyRate'));
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $courseCategory = CourseCategory::all()->map(function ($item) {
        $names = json_decode($item->category_name, true);
        $item->category_name = $names[app()->getLocale()] ?? reset($names) ?? 'No Name';
        return $item;
    });

    $instructor = Instructor::all();
    $course = Course::findOrFail(encryptor('decrypt', $id));
    return view('backend.course.courses.edit', compact('courseCategory', 'instructor', 'course'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
{
    try {
        DB::beginTransaction();

        $course = Course::findOrFail(encryptor('decrypt', $id));

        $course->course_category_id = $request->categoryId;
        $course->instructor_id = $request->instructorId;
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

        if ($request->hasFile('thumbnail_video_file')) {
            $videoPath = $request->file('thumbnail_video_file')->store('uploads/videos', 'public');
            $course->thumbnail_video = $videoPath;
        } elseif ($request->filled('thumbnail_video_url')) {
            $course->thumbnail_video = $request->input('thumbnail_video_url');
        }
        // Если ни файл, ни URL — старое значение сохраняется

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

        // Обновляем переводы: удаляем старые и добавляем новые
        $course->translations()->delete();
        $translations = $request->input('translations', []);
        foreach ($translations as $locale => $fields) {
            $course->translations()->create([
                'locale' => $locale,
                'title' => $fields['title'] ?? '',
                'description' => $fields['description'] ?? '',
                'prerequisites' => $fields['prerequisites'] ?? '',
            ]);
        }

        DB::commit();

        return redirect()->route('course.index')->with('success', 'Course updated successfully.');

    } catch (Exception $e) {
        DB::rollBack();
        report($e);
        return redirect()->back()->withInput()->with('error', 'Please try again');
    }
}


    public function updateforAdmin(UpdateRequest $request, $id)
{
    try {
        DB::beginTransaction();

        $course = Course::findOrFail(encryptor('decrypt', $id));

        // Обновляем основные поля (не переводы)
        $course->course_category_id = $request->categoryId;
        $course->instructor_id = $request->instructorId;
        $course->type = $request->courseType;
        $course->price = $request->coursePrice;
        $course->old_price = $request->courseOldPrice;
        $course->subscription_price = $request->subscription_price; // исправлено имя
        $course->start_from = $request->start_from;
        $course->duration = $request->duration;
        $course->lesson = $request->lesson;
        $course->difficulty = $request->courseDifficulty;
        $course->course_code = $request->course_code;
        $course->tag = $request->tag;
        $course->status = $request->status;
        $course->language = 'en';

        if ($request->hasFile('thumbnail_video_file')) {
            $videoPath = $request->file('thumbnail_video_file')->store('uploads/videos', 'public');
            $course->thumbnail_video = $videoPath;
        } elseif ($request->filled('thumbnail_video_url')) {
            $course->thumbnail_video = $request->input('thumbnail_video_url');
        }

        if ($request->hasFile('image')) {
            $imageName = rand(111, 999) . time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/courses'), $imageName);
            $course->image = $imageName;
        }

        if ($request->hasFile('thumbnail_image')) {
            $thumbnailImageName = rand(111, 999) . time() . '.' . $request->thumbnail_image->extension();
            $request->thumbnail_image->move(public_path('uploads/courses/thumbnails'), $thumbnailImageName);
            $course->thumbnail_image = $thumbnailImageName;
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
            ]);
        }

        DB::commit();

        return redirect()->route('courseList')->with('success', 'Data Saved');

    } catch (Exception $e) {
        DB::rollBack();
        // при необходимости добавить логирование $e
        return redirect()->back()->withInput()->with('error', 'Please try again');
    }
}
}
