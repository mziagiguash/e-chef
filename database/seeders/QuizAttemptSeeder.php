<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\QuizAttempt;

class QuizAttemptSeeder extends Seeder
{
    public function run()
    {
        // Отключаем проверку внешних ключей
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Очищаем таблицу
        QuizAttempt::truncate();

        // Включаем проверку внешних ключей обратно
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Создаем 50 попыток через фабрику
        $attempts = QuizAttempt::factory()->count(50)->create();

        $this->command->info("✅ Created {$attempts->count()} quiz attempts");

        // Статистика
        $completed = QuizAttempt::where('status', QuizAttempt::STATUS_COMPLETED)->count();
        $inProgress = QuizAttempt::where('status', QuizAttempt::STATUS_IN_PROGRESS)->count();
        $expired = QuizAttempt::where('status', QuizAttempt::STATUS_EXPIRED)->count();

        $this->command->info("📊 Statistics:");
        $this->command->info("   • Completed: {$completed}");
        $this->command->info("   • In Progress: {$inProgress}");
        $this->command->info("   • Expired: {$expired}");
    }
}
