<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTranslation;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
public function index(Request $request)
{
    $locale = $request->get('lang', app()->getLocale());

    $events = Event::with(['translations' => function($q) use ($locale) {
        $q->where('locale', $locale);
    }])->latest()->get();

    return view('backend.event.index', compact('events', 'locale'));
}

    public function create()
    {
        $locales = config('app.available_locales', ['en', 'ru', 'ka']);
        $currentLocale = request('lang', app()->getLocale());

        return view('backend.event.create', compact('locales', 'currentLocale'));
    }

    public function store(Request $request)
{
    try {
        $event = new Event;

        // Сохраняем только нетекстовые поля
        $event->date = $request->date;

        if ($request->hasFile('image')) {
            $imageName = rand(999, 111) . time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/events'), $imageName);
            $event->image = $imageName;
        }

        if ($event->save()) {
            // Сохраняем переводы для ВСЕХ языков
            $locales = ['en', 'ru', 'ka']; // используем коды, а не флаги

            foreach ($locales as $localeCode) {
                $translation = new EventTranslation([
                    'locale' => $localeCode, // en, ru, ka
                    'title' => $request->input("title_$localeCode", ''),
                    'description' => $request->input("description_$localeCode", ''),
                    'location' => $request->input("location_$localeCode", ''),
                    'topic' => $request->input("topic_$localeCode", ''),
                    'goal' => $request->input("goal_$localeCode", ''),
                    'hosted_by' => $request->input("hosted_by_$localeCode", ''),
                ]);

                $event->translations()->save($translation);
            }

            $this->notice::success('Data Saved');
            return redirect()->route('event.index');
        }
    } catch (Exception $e) {
        $this->notice::error('Error: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

    public function edit($id)
    {
        $event = Event::with('translations')->findOrFail($id);
        $locales = config('app.available_locales', ['en', 'ru', 'ka']);
        $currentLocale = request('lang', app()->getLocale());

        // Преобразуем переводы в массив для удобства
        $translations = [];
        foreach ($event->translations as $translation) {
            $translations[$translation->locale] = $translation;
        }

        return view('backend.event.edit', compact('event', 'locales', 'currentLocale', 'translations'));
    }

    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            // Обновляем нетекстовые поля
            $event->date = $request->date;

            if ($request->hasFile('image')) {
                // Удаляем старое изображение если оно есть
                if ($event->image && file_exists(public_path('uploads/events/' . $event->image))) {
                    unlink(public_path('uploads/events/' . $event->image));
                }

                $imageName = rand(999, 111) . time() . '.' . $request->image->extension();
                $request->image->move(public_path('uploads/events'), $imageName);
                $event->image = $imageName;
            }

            if ($event->save()) {
                // Обновляем переводы
                $locales = config('app.available_locales', ['en', 'ru', 'ka']);

                foreach ($locales as $locale) {
                    $translationData = [
                        'title' => $request->input("title_$locale", ''),
                        'description' => $request->input("description_$locale", ''),
                        'location' => $request->input("location_$locale", ''),
                        'topic' => $request->input("topic_$locale", ''),
                        'goal' => $request->input("goal_$locale", ''),
                        'hosted_by' => $request->input("hosted_by_$locale", ''),
                    ];

                    $event->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $translationData
                    );
                }

                $this->notice::success('Event updated successfully');
                return redirect()->route('event.index', ['lang' => $request->input('current_locale', app()->getLocale())]);
            }

            $this->notice::error('Please try again');
            return redirect()->back()->withInput();

        } catch (Exception $e) {
            \Log::error('Event update error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);

            // Удаляем изображение если оно есть
            if ($event->image && file_exists(public_path('uploads/events/' . $event->image))) {
                unlink(public_path('uploads/events/' . $event->image));
            }

            // Удаляем связанные переводы
            $event->translations()->delete();

            if ($event->delete()) {
                $this->notice::success('Event deleted successfully');
                return redirect()->back();
            }

            $this->notice::error('Failed to delete event');
            return redirect()->back();

        } catch (Exception $e) {
            \Log::error('Event delete error: ' . $e->getMessage());
            $this->notice::error('Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
