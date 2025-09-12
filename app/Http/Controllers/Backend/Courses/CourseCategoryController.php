<?php

namespace App\Http\Controllers\Backend\Courses;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\Course\CourseCategory\AddNewRequest;
use App\Http\Requests\Backend\Course\CourseCategory\UpdateRequest;
use Exception;
use File;
use Illuminate\Support\Facades\DB;

class CourseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $query = CourseCategory::with('translations'); // предзагрузка переводов

    if ($search = $request->input('search')) {
        $query->whereHas('translations', function($q) use ($search) {
            $q->where('category_name', 'like', "%{$search}%");
        });
    }

    $data = $query->paginate(10);
    $appLocale = app()->getLocale();
    $locales = config('app.available_locales', ['en', 'ru', 'ka']);

    return view('backend.course.courseCategory.index', compact('data', 'appLocale', 'locales'));
}

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $locales = config('app.available_locales', ['en', 'ru', 'ka']);
    return view('backend.course.courseCategory.create', compact('locales'));
}

public function edit($id)
{
    $data = CourseCategory::with('translations')->findOrFail($id);
    $locales = config('app.available_locales', ['en', 'ru', 'ka']);
    return view('backend.course.courseCategory.edit', compact('data', 'locales'));
}
    /**
     * Store a newly created resource in storage.
     */

public function store(AddNewRequest $request)
{
    try {
        $data = new CourseCategory;
        $data->category_status = $request->category_status;

        if ($request->hasFile('category_image')) {
            $imageName = rand(111, 999) . time() . '.' . $request->category_image->extension();
            $request->category_image->move(public_path('uploads/courseCategories'), $imageName);
            $data->category_image = $imageName;
        }

        if ($data->save()) {
            // Сохраняем переводы
            foreach ($request->translations as $locale => $translationData) {
                $data->translations()->create([
                    'locale' => $locale,
                    'category_name' => $translationData['category_name']
                ]);
            }

            return redirect()->route('courseCategory.index')->with('success', 'Data Saved');
        }
    } catch (Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Please try again: ' . $e->getMessage());
    }
}

public function update(UpdateRequest $request, $id)
{
    DB::beginTransaction();

    try {
        $data = CourseCategory::with('translations')->findOrFail($id);
        $data->category_status = $request->category_status;

        // Обновление изображения...
        if ($request->hasFile('category_image')) {
            $imageName = rand(111, 999) . time() . '.' . $request->category_image->extension();
            $request->category_image->move(public_path('uploads/courseCategories'), $imageName);

            $oldImage = public_path('uploads/courseCategories/') . $data->category_image;
            if (File::exists($oldImage) && $data->category_image) {
                File::delete($oldImage);
            }

            $data->category_image = $imageName;
        }

        $data->save();

        // Удаляем старые переводы и создаем новые
        $data->translations()->delete();

        $locales = ['en', 'ru', 'ka'];
        foreach ($locales as $locale) {
            if ($request->has("translations.{$locale}.category_name")) {
                $data->translations()->create([
                    'locale' => $locale,
                    'category_name' => $request->input("translations.{$locale}.category_name")
                ]);
            }
        }

        DB::commit();
        return redirect()->route('courseCategory.index')->with('success', 'Category updated successfully');

    } catch (Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $data = CourseCategory::findOrFail($id);
            $image_path = public_path('uploads/courseCategories/') . $data->category_image;

            // Удаляем все переводы
            $data->translations()->delete();

            if ($data->delete()) {
                if (File::exists($image_path) && $data->category_image) {
                    File::delete($image_path);
                }
                return redirect()->back()->with('success', 'Category deleted successfully');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }

    public function frontIndex()
    {
        // Берём все категории с курсами
        $categories = CourseCategory::with(['translations'])
            ->withCount(['courses' => function($q) {
                $q->where('status', 2);
            }])
            ->get();

        return view('frontend.categories.index', compact('categories'));
    }
}
