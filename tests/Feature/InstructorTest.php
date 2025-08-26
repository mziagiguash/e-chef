<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class InstructorTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    /** @test */
    public function it_creates_instructor_with_translations_and_user()
    {
        // Авторизуемся под админом
        $admin = User::factory()->create([
            'email' => 'superadmin@example.com',
            'role_id' => 1, // Super Admin
        ]);
        $this->actingAs($admin);

        // Отправляем форму создания инструктора
        $response = $this->post('/admin/instructor', [
            'name' => [
                'en' => 'Test Instructor',
                'ru' => 'Тестовый Инструктор',
                'ka' => 'ტესტი მასწავლებელი',
            ],
            'designation' => [
                'en' => 'Senior Teacher',
                'ru' => 'Старший учитель',
                'ka' => 'გამოცდილი მასწავლებელი',
            ],
            'bio' => [
                'en' => 'Bio EN',
                'ru' => 'Био RU',
                'ka' => 'ბიო KA',
            ],
            'contactNumber' => '57133388166',
            'emailAddress' => 'instructor@gmail.com',
            'roleId' => 3, // Instructor
            'status' => 1,
            'access_block' => 0,
            'password' => 'password123',
        ]);

        // Проверяем редирект на список инструкторов
        $response->assertRedirect('/admin/instructor');

        // Проверяем, что инструктор добавлен в базу
        $this->assertDatabaseHas('instructors', [
            'email' => 'instructor@gmail.com',
        ]);

        $instructor = Instructor::where('email', 'instructor@gmail.com')->first();

        // Проверяем переводы
        $this->assertDatabaseHas('instructor_translations', [
            'instructor_id' => $instructor->id,
            'locale' => 'en',
            'name' => 'Test Instructor',
        ]);

        $this->assertDatabaseHas('instructor_translations', [
            'instructor_id' => $instructor->id,
            'locale' => 'ru',
            'name' => 'Тестовый Инструктор',
        ]);

        $this->assertDatabaseHas('instructor_translations', [
            'instructor_id' => $instructor->id,
            'locale' => 'ka',
            'name' => 'ტესტი მასწავლებელი',
        ]);

        // Проверяем, что пользователь создан
        $this->assertDatabaseHas('users', [
            'email' => 'instructor@gmail.com',
            'role_id' => 3,
        ]);
    }
}
