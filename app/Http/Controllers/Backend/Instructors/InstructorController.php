<?php

namespace App\Http\Controllers\Backend\Instructors;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use App\Models\InstructorTranslation;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\Backend\Instructors\AddNewRequest;
use App\Http\Requests\Backend\Instructors\UpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Exception;

class InstructorController extends Controller
{
public function index()
{
    $instructors = Instructor::with('translations', 'role')->get();
    return view('backend.instructor.index', compact('instructors'));
}

    public function create()
    {
        $role = Role::get();
        $locales = config('app.available_locales', ['en', 'ru', 'ka']);
        return view('backend.instructor.create', compact('role', 'locales'));
    }

    public function store(AddNewRequest $request)
    {
        $data = $request->validated();

        // Обработка изображения
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/users/'), $imageName);
            $data['image'] = $imageName;
        } else {
            $data['image'] = 'default_instructor.jpg';
        }

        // Хеширование пароля
        $data['password'] = Hash::make($request->password);

        // Создаем инструктора
        $instructor = Instructor::create($data);

        // Создаем переводы
        if ($request->has('translations')) {
            foreach ($request->translations as $locale => $translationData) {
                $translationData['locale'] = $locale;
                $instructor->translations()->create($translationData);
            }
        }

        return redirect()->route('instructor.index')->with('success', 'Instructor created successfully');
    }

    public function edit($id)
    {
        $instructor = Instructor::with('translations')->findOrFail(encryptor('decrypt', $id));
        $role = Role::all();
        return view('backend.instructor.edit', compact('instructor', 'role'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $instructor = Instructor::findOrFail(encryptor('decrypt', $id));
        $data = $request->validated();

        // Обработка изображения
        if ($request->hasFile('image')) {
            // Удаляем старое изображение если оно не дефолтное
            if ($instructor->image && $instructor->image !== 'default_instructor.jpg') {
                File::delete(public_path('uploads/users/' . $instructor->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/users/'), $imageName);
            $data['image'] = $imageName;
        }

        // Обновляем пароль если указан
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // Обновляем основные данные
        $instructor->update($data);

        // Обновляем переводы
        if ($request->has('translations')) {
            foreach ($request->translations as $locale => $translationData) {
                if (isset($translationData['id'])) {
                    // Обновляем существующий перевод
                    $translation = InstructorTranslation::find($translationData['id']);
                    if ($translation) {
                        $translation->update($translationData);
                    }
                } else {
                    // Создаем новый перевод
                    $translationData['locale'] = $locale;
                    $instructor->translations()->create($translationData);
                }
            }
        }

        return redirect()->route('instructor.index')->with('success', 'Instructor updated successfully');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $instructor = Instructor::findOrFail(encryptor('decrypt', $id));

            // Удаляем изображение если оно не дефолтное
            if ($instructor->image && $instructor->image !== 'default_instructor.jpg' &&
                File::exists(public_path('uploads/users/' . $instructor->image))) {
                File::delete(public_path('uploads/users/' . $instructor->image));
            }

            // Удаляем переводы
            $instructor->translations()->delete();

            $instructor->delete();

            // Удаляем пользователя
            $user = User::where('instructor_id', $instructor->id)->first();
            if ($user) {
                if ($user->image && $user->image !== 'default_instructor.jpg' &&
                    File::exists(public_path('uploads/users/' . $user->image))) {
                    File::delete(public_path('uploads/users/' . $user->image));
                }
                $user->delete();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Instructor deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

public function frontShow($id)
{
    $instructor = Instructor::with('translations')->findOrFail($id);
    return view('frontend.instructorProfile', compact('instructor'));
}
}
