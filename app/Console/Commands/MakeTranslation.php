<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeTranslation extends Command
{
    protected $signature = 'make:translation {model} {--fields=}';
    protected $description = 'Создаёт модель и миграцию для переводов и добавляет методы в исходную модель';

    public function handle()
    {
        $model = $this->argument('model');
        $fieldsOption = $this->option('fields'); // comma-separated fields
        $fields = $fieldsOption ? explode(',', $fieldsOption) : [];

        $translationModel = $model . 'Translation';
        $tableName = Str::snake($model) . '_translations';

        // 1️⃣ Создать модель перевода
        $modelPath = app_path("Models/{$translationModel}.php");
        if (!File::exists($modelPath)) {
            $fillableFields = implode("', '", $fields);
            $modelContent = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$translationModel} extends Model
{
    protected \$fillable = ['{$model}_id', 'locale', '{$fillableFields}'];

    public function {$model}()
    {
        return \$this->belongsTo({$model}::class);
    }
}
EOD;
            File::put($modelPath, $modelContent);
            $this->info("Модель {$translationModel} создана.");
        } else {
            $this->warn("Модель {$translationModel} уже существует.");
        }

        // 2️⃣ Создать миграцию
        $timestamp = date('Y_m_d_His');
        $migrationName = "create_{$tableName}_table";
        $migrationPath = database_path("migrations/{$timestamp}_{$migrationName}.php");

        $migrationFields = '';
        foreach ($fields as $field) {
            $migrationFields .= "\$table->string('{$field}')->nullable();\n            ";
        }

        $migrationContent = <<<EOD
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->unsignedBigInteger('{$model}_id');
            \$table->string('locale');
            {$migrationFields}
            \$table->timestamps();

            \$table->foreign('{$model}_id')->references('id')->on(''.Str::snake(\$model).'s'')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
EOD;

        File::put($migrationPath, $migrationContent);
        $this->info("Миграция {$migrationName} создана в {$migrationPath}.");

        // 3️⃣ Добавить методы в исходную модель
        $modelFile = app_path("Models/{$model}.php");
        if (File::exists($modelFile)) {
            $modelContent = File::get($modelFile);

            // Проверим, есть ли уже методы
            if (!str_contains($modelContent, 'function translations()')) {
                $insertMethods = <<<EOD

    // Translations
    public function translations()
    {
        return \$this->hasMany({$translationModel}::class);
    }

    public function translation(\$locale = null)
    {
        \$locale = \$locale ?: app()->getLocale();
        return \$this->hasOne({$translationModel}::class)->where('locale', \$locale);
    }
EOD;
                // Вставляем перед последней закрывающей скобкой класса
                $modelContent = preg_replace('/}\s*$/', $insertMethods . "\n}", $modelContent);
                File::put($modelFile, $modelContent);
                $this->info("Методы translations() и translation() добавлены в {$model}.php");
            } else {
                $this->warn("Методы уже существуют в {$model}.php");
            }
        } else {
            $this->warn("Исходная модель {$model}.php не найдена.");
        }
    }
}
