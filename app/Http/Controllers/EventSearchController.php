<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventSearchController extends Controller
{
    public function index(Request $request, $locale = null)
    {
        // Если locale передан в URL, используем его, иначе из запроса или дефолтный
        $currentLocale = $locale ?? $request->get('lang', app()->getLocale());

        // Устанавливаем локаль приложения
        app()->setLocale($currentLocale);

        $search = $request->get('search');

        $events = Event::with(['translations' => function($q) use ($currentLocale) {
            $q->where('locale', $currentLocale);
        }])
        ->when($search, function($query) use ($search, $currentLocale) {
            $query->whereHas('translations', function($q) use ($search, $currentLocale) {
                $q->where('locale', $currentLocale)
                  ->where(function($subQuery) use ($search) {
                      $subQuery->where('title', 'like', "%$search%")
                               ->orWhere('description', 'like', "%$search%")
                               ->orWhere('location', 'like', "%$search%")
                               ->orWhere('topic', 'like', "%$search%");
                  });
            });
        })
        ->orderBy('date', 'desc')
        ->paginate(12);

        $locales = ['en' => 'English', 'ru' => 'Русский', 'ka' => 'ქართული'];

        return view('frontend.event-search', compact('events', 'currentLocale', 'locales', 'search'));
    }
}
