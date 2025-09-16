<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Instructor;
use Illuminate\Http\Request;

class FrontendInstructorController extends Controller
{
    public function index(Request $request, $locale = null)
    {
        // Установка языка из параметра маршрута или query string
        $lang = $locale ?? $request->get('lang', session('locale', 'en'));
        if (in_array($lang, ['en', 'ru', 'ka'])) {
            app()->setLocale($lang);
            session()->put('locale', $lang);
        }

        $instructors = Instructor::where('status', 1)
                               ->with('translations')
                               ->paginate(6);

        return view('frontend.instructors.index', compact('instructors'));
    }

    public function show(Request $request, $locale = null, $id)
    {
        // Установка языка из параметра маршрута или query string
        $lang = $locale ?? $request->get('lang', session('locale', 'en'));
        if (in_array($lang, ['en', 'ru', 'ka'])) {
            app()->setLocale($lang);
            session()->put('locale', $lang);
        }

        $instructor = Instructor::where('id', $id)
                               ->where('status', 1)
                               ->with(['translations', 'courses'])
                               ->firstOrFail();

        return view('frontend.instructors.show', compact('instructor'));
    }
}
