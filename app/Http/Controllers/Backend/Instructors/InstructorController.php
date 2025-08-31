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

        return view('backend.instructor.index', [
            'instructor' => $instructors,
        ]);
    }

    public function create()
    {
        $role = Role::get();
        return view('backend.instructor.create', compact('role'));
    }

    public function store(AddNewRequest $request)
    {
        DB::beginTransaction();
        try {
            $instructor = new Instructor();
            $instructor->contact      = $request->contact;
            $instructor->email        = $request->email;
            $instructor->role_id      = $request->role_id;
            $instructor->status       = $request->status;
            $instructor->language     = 'en'; // базовый язык
            $instructor->access_block = $request->access_block;
            $instructor->password     = Hash::make($request->password);

            if ($request->hasFile('image')) {
                $imageName = 'instructor_' . time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/users'), $imageName);
                $instructor->image = $imageName;
            }

            $instructor->save();

            // сохраняем переводы
            foreach (['en', 'ru', 'ka'] as $locale) {
                InstructorTranslation::create([
                    'instructor_id' => $instructor->id,
                    'locale'        => $locale,
                    'name'          => $request->name[$locale] ?? null,
                    'designation'   => $request->designation[$locale] ?? null,
                    'title'         => $request->title[$locale] ?? null,
                    'bio'           => $request->bio[$locale] ?? null,
                ]);
            }

            // создаём пользователя
            $user = new User();
            $user->instructor_id = $instructor->id;
            $user->name          = $request->name['en'] ?? '';
            $user->email         = $request->email;
            $user->contact       = $request->contact;
            $user->role_id       = $request->role_id;
            $user->status        = $request->status;
            $user->password      = Hash::make($request->password);
            if (isset($imageName)) $user->image = $imageName;
            $user->save();

            DB::commit();
            return redirect()->route('instructor.index')->with('success', 'Instructor created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $instructor = Instructor::with('translations')->findOrFail($id);
        $role = Role::select('id','name')->distinct()->get();

        return view('backend.instructor.edit', compact('instructor','role'));
    }

public function update(UpdateRequest $request, $id)
{
    DB::beginTransaction();
    try {
        $instructor = Instructor::findOrFail($id);

        $instructor->contact      = $request->contact;
        $instructor->email        = $request->email;
        $instructor->role_id      = $request->role_id;
        $instructor->status       = $request->status;
        $instructor->access_block = $request->access_block;

        if ($request->password) {
            $instructor->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($instructor->image && File::exists(public_path('uploads/users/' . $instructor->image))) {
                File::delete(public_path('uploads/users/' . $instructor->image));
            }
            $imageName = 'instructor_' . time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/users'), $imageName);
            $instructor->image = $imageName;
        }

        $instructor->save();

        // Переводы
        foreach (['en','ru','ka'] as $locale) {
            $translation = $instructor->translations()->firstOrNew(['locale' => $locale]);
            $translation->name        = $request->input("name.$locale");
            $translation->designation = $request->input("designation.$locale");
            $translation->title       = $request->input("title.$locale");     // теперь колонка есть
            $translation->bio         = $request->input("bio.$locale");
            $translation->save();
        }

        // Пользователь, связанный с инструктором
        $user = User::where('instructor_id', $instructor->id)->first();
        if ($user) {
            $user->name     = $request->input('name.en', $user->name);
            $user->email    = $request->email;
            $user->contact  = $request->contact;
            $user->role_id  = $request->role_id;
            $user->status   = $request->status;
            if ($request->password) $user->password = Hash::make($request->password);
            if (isset($imageName)) $user->image = $imageName;
            $user->save();
        }

        DB::commit();
        return redirect()->route('instructor.index')->with('success', 'Instructor updated successfully.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('error', 'Error: '.$e->getMessage());
    }
}


    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);

        if ($instructor->image && File::exists(public_path('uploads/users/' . $instructor->image))) {
            File::delete(public_path('uploads/users/' . $instructor->image));
        }

        // удаляем переводы
        $instructor->translations()->delete();

        $instructor->delete();

        $user = User::where('instructor_id', $instructor->id)->first();
        if ($user) $user->delete();

        return redirect()->back()->with('success', 'Instructor deleted successfully.');
    }

public function frontShow($id)
{
    $id = encryptor('decrypt', $id); // расшифровка
    $instructor = Instructor::with('translations')->findOrFail($id);
    return view('frontend.instructorProfile', compact('instructor'));
}

}
