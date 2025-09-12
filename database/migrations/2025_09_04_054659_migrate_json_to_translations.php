<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Переносим данные из JSON полей в таблицу переводов
        $instructors = DB::table('instructors')->get();

        foreach ($instructors as $instructor) {
            $name = json_decode($instructor->name, true) ?? [];
            $bio = json_decode($instructor->bio, true) ?? [];
            $title = json_decode($instructor->title, true) ?? [];
            $designation = json_decode($instructor->designation, true) ?? [];

            foreach (['en', 'ka', 'ru'] as $locale) {
                if (isset($name[$locale]) || isset($bio[$locale]) || isset($title[$locale]) || isset($designation[$locale])) {
                    DB::table('instructor_translations')->insert([
                        'instructor_id' => $instructor->id,
                        'locale' => $locale,
                        'name' => $name[$locale] ?? null,
                        'bio' => $bio[$locale] ?? null,
                        'designation' => $designation[$locale] ?? null,
                        'title' => $title[$locale] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Очищаем JSON поля (опционально, но рекомендуется)
        DB::table('instructors')->update([
            'name' => null,
            'bio' => null,
            'title' => null,
            'designation' => null,
        ]);
    }

    public function down()
    {
        // Обратная миграция - вернуть данные в JSON поля
        $translations = DB::table('instructor_translations')
            ->select('instructor_id', 'locale', 'name', 'bio', 'designation', 'title')
            ->get()
            ->groupBy('instructor_id');

        foreach ($translations as $instructorId => $translationGroup) {
            $name = [];
            $bio = [];
            $designation = [];
            $title = [];

            foreach ($translationGroup as $translation) {
                $name[$translation->locale] = $translation->name;
                $bio[$translation->locale] = $translation->bio;
                $designation[$translation->locale] = $translation->designation;
                $title[$translation->locale] = $translation->title;
            }

            DB::table('instructors')
                ->where('id', $instructorId)
                ->update([
                    'name' => json_encode($name),
                    'bio' => json_encode($bio),
                    'designation' => json_encode($designation),
                    'title' => json_encode($title),
                ]);
        }

        // Очищаем таблицу переводов
        DB::table('instructor_translations')->truncate();
    }
};
