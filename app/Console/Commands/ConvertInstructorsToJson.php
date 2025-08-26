<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Instructor;

class ConvertInstructorsToJson extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'instructors:convert-json';

    /**
     * The console command description.
     */
    protected $description = 'Convert all instructors names to JSON format';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $instructors = Instructor::all();
        foreach ($instructors as $instructor) {
            $names = ['en' => $instructor->name]; // Добавь другие языки, если нужно
            $instructor->name = json_encode($names, JSON_UNESCAPED_UNICODE);
            $instructor->save();
        }

        $this->info('Instructors converted to JSON successfully.');
    }
}
