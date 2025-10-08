<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Http\Requests\Students\Auth\SignUpRequest;
use App\Http\Requests\Students\Auth\SignInRequest;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{
    // Registration form
    public function signUpForm()
    {
        return view('students.auth.register');
    }

    // Registration store
    public function signUpStore(SignUpRequest $request)
    {
        try {
            $student = new Student();
            $student->name = $request->name;
            $student->email = $request->email;
            $student->password = Hash::make($request->password);

            if ($student->save()) {
                $this->setSession($student);
                return redirect()->route('studentdashboard', ['locale' => app()->getLocale()])
                                 ->with('success', 'Successfully Registered and Logged In');
            }

            return redirect()->back()->withInput()->with('error', 'Please try again');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred. Please try again!');
        }
    }

    // Login form
    public function signInForm()
    {
        return view('students.auth.login');
    }

    // Login check
    public function signInCheck(SignInRequest $request)
    {
        try {
            $student = Student::where('email', $request->email)->first();

            if (!$student || $student->status != 1 || !Hash::check($request->password, $student->password)) {
                return redirect()->back()->withInput()->with('error', 'Username or Password is wrong!');
            }

            $this->setSession($student);

            return redirect()->route('studentdashboard', ['locale' => app()->getLocale()])
                             ->with('success', 'Successfully Logged In');

        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'An error occurred. Please try again!');
        }
    }

    // Set student session
    protected function setSession(Student $student)
    {
        session()->put([
            'userId' => encryptor('encrypt', $student->id),
            'userName' => encryptor('encrypt', $student->name),
            'emailAddress' => encryptor('encrypt', $student->email),
            'studentLogin' => true,
            'image' => $student->image ?? 'No Image Found',
            'student_id' => $student->id
        ]);
    }

    // Logout
    public function signOut()
    {
        session()->flush();
        return redirect()->route('studentLogin', ['locale' => app()->getLocale()])
                         ->with('success', 'Successfully Logged Out');
    }
}
