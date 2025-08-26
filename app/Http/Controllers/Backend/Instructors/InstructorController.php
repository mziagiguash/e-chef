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
            $instructor->contact = $request->contact;
            $instructor->email = $request->email;
            $instructor->role_id = $request->role_id;
            $instructor->status = $request->status;
            $instructor->language = 'en';
            $instructor->access_block = $request->access_block;
            $instructor->password = Hash::make($request->password);

            if ($request->hasFile('image')) {
                $imageName = 'instructor_' . time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/users'), $imageName);
                $instructor->image = $imageName;
            }

            // Сохраняем все переводы как JSON
            $instructor->name = json_encode([
    'en' => $request->name['en'] ?? null,
    'ru' => $request->name['ru'] ?? null,
    'ka' => $request->name['ka'] ?? null,
]);
            $instructor->designation = json_encode([
    'en' => $request->designation['en'] ?? null,
    'ru' => $request->designation['ru'] ?? null,
    'ka' => $request->designation['ka'] ?? null,
]);
            $instructor->title = json_encode([
    'en' => $request->title['en'] ?? null,
    'ru' => $request->title['ru'] ?? null,
    'ka' => $request->title['ka'] ?? null,
]);
            $instructor->bio = json_encode([
    'en' => $request->bio['en'] ?? null,
    'ru' => $request->bio['ru'] ?? null,
    'ka' => $request->bio['ka'] ?? null,
]);

            $instructor->save();

            // Создаём пользователя
            $user = new User();
            $user->instructor_id = $instructor->id;
            $user->name = $request->name['en'] ?? '';
            $user->email = $request->email;
            $user->contact = $request->contact;
            $user->role_id = $request->role_id;
            $user->status = $request->status;
            $user->password = Hash::make($request->password);
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
    $id = encryptor('decrypt', $id); // расшифровка
    $instructor = Instructor::find($id);

    if (!$instructor) {
        abort(404, "Instructor not found for id={$id}");
    }

    // Декодируем JSON-поля
    $instructor->name = json_decode($instructor->name, true);
    $instructor->designation = json_decode($instructor->designation, true);
    $instructor->title = json_decode($instructor->title, true);
    $instructor->bio = json_decode($instructor->bio, true);

    $role = Role::all();

    return view('backend.instructor.edit', compact('instructor','role'));
}



    public function update(UpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $id = encryptor('decrypt', $id);
            $instructor = Instructor::findOrFail($id);
            $instructor->contact = $request->contact;
            $instructor->email = $request->email;
            $instructor->role_id = $request->role_id;
            $instructor->status = $request->status;
            $instructor->access_block = $request->access_block;
            $instructor->language = 'en';
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

            // Обновляем JSON-поля
            $instructor->name = json_encode([
    'en' => $request->name['en'] ?? null,
    'ru' => $request->name['ru'] ?? null,
    'ka' => $request->name['ka'] ?? null,
]);
            $instructor->designation = json_encode([
    'en' => $request->designation['en'] ?? null,
    'ru' => $request->designation['ru'] ?? null,
    'ka' => $request->designation['ka'] ?? null,
]);
            $instructor->title = json_encode([
    'en' => $request->title['en'] ?? null,
    'ru' => $request->title['ru'] ?? null,
    'ka' => $request->title['ka'] ?? null,
]);
            $instructor->bio = json_encode([
    'en' => $request->bio['en'] ?? null,
    'ru' => $request->bio['ru'] ?? null,
    'ka' => $request->bio['ka'] ?? null,
]);

            $instructor->save();

            // Обновляем пользователя
            $user = User::where('instructor_id', $instructor->id)->first();
            if ($user) {
                $user->name = $request->name['en'] ?? $user->name;
                $user->email = $request->email;
                $user->contact = $request->contact;
                $user->role_id = $request->role_id;
                $user->status = $request->status;
                if ($request->password) $user->password = Hash::make($request->password);
                if (isset($imageName)) $user->image = $imageName;
                $user->save();
            }

            DB::commit();
            return redirect()->route('instructor.index')->with('success', 'Instructor updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);

        if ($instructor->image && File::exists(public_path('uploads/users/' . $instructor->image))) {
            File::delete(public_path('uploads/users/' . $instructor->image));
        }

        $instructor->delete();

        $user = User::where('instructor_id', $instructor->id)->first();
        if ($user) $user->delete();

        return redirect()->back()->with('success', 'Instructor deleted successfully.');
    }

    public function frontShow($id)
    {
        $instructor = Instructor::with('translations')->findOrFail($id);
        return view('frontend.instructorProfile', compact('instructor'));
    }
}
