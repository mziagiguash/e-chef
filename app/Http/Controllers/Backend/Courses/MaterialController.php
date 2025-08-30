<?php

namespace App\Http\Controllers\Backend\Courses;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialTranslation;
use App\Models\Lesson;
use App\Http\Requests\Backend\Course\Materials\AddNewRequest;
use App\Http\Requests\Backend\Course\Materials\UpdateRequest;
use Exception;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::paginate(10);
        return view('backend.course.material.index', compact('materials'));
    }

    public function create()
    {
        $lessons = Lesson::all();
        return view('backend.course.material.create', compact('lessons'));
    }

    public function store(AddNewRequest $request)
    {
        try {
            $data = $request->validated();

            $material = new Material();
            $material->lesson_id = $data['lessonId'];
            $material->type = $data['materialType'];
            $material->content_url = $data['contentURL'] ?? null;

            // Загружаем файл
            if ($request->hasFile('content')) {
                $fileName = rand(111, 999) . time() . '.' . $request->content->extension();
                $request->content->move(public_path('uploads/courses/contents'), $fileName);
                $material->content = $fileName;
            }

            $material->save();

            // Сохраняем переводы
            foreach ($data['materialTitle'] as $locale => $title) {
                $contentText = $data['content_text'][$locale] ?? null;

                $material->translations()->create([
                    'locale' => $locale,
                    'title' => $title,
                    'content_text' => $contentText,
                ]);
            }

            return redirect()->route('material.index')->with('success', 'Material created successfully.');
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->withInput()->with('error', 'Please try again');
        }
    }

    public function edit($id)
    {
        $material = Material::findOrFail(encryptor('decrypt', $id));
        $lessons = Lesson::all();
        return view('backend.course.material.edit', compact('material', 'lessons'));
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $material = Material::findOrFail(encryptor('decrypt', $id));
            $material->lesson_id = $data['lessonId'];
            $material->type = $data['materialType'];
            $material->content_url = $data['contentURL'] ?? null;

            if ($request->hasFile('content')) {
                $fileName = rand(111, 999) . time() . '.' . $request->content->extension();
                $request->content->move(public_path('uploads/courses/contents'), $fileName);
                $material->content = $fileName;
            }

            $material->save();

            // Обновляем переводы
            foreach ($data['materialTitle'] as $locale => $title) {
                $contentText = $data['content_text'][$locale] ?? null;

                $translation = $material->translations()->where('locale', $locale)->first();
                if ($translation) {
                    $translation->update([
                        'title' => $title,
                        'content_text' => $contentText,
                    ]);
                } else {
                    $material->translations()->create([
                        'locale' => $locale,
                        'title' => $title,
                        'content_text' => $contentText,
                    ]);
                }
            }

            return redirect()->route('material.index')->with('success', 'Material updated successfully.');
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->withInput()->with('error', 'Please try again');
        }
    }

    public function destroy($id)
    {
        $material = Material::findOrFail(encryptor('decrypt', $id));
        $material->delete();
        return redirect()->back()->with('success', 'Material deleted!');
    }
}
