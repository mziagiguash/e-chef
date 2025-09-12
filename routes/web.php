<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Setting\AuthenticationController as auth;
use App\Http\Controllers\Backend\Setting\UserController as user;
use App\Http\Controllers\Backend\Setting\DashboardController as dashboard;
use App\Http\Controllers\Backend\Setting\RoleController as role;
use App\Http\Controllers\Backend\Setting\PermissionController as permission;
use App\Http\Controllers\Backend\Students\StudentController as student;
use App\Http\Controllers\Backend\Instructors\InstructorController;
use App\Http\Controllers\Backend\Courses\CourseCategoryController as courseCategory;
use App\Http\Controllers\Backend\Courses\CourseController as course;
use App\Http\Controllers\Backend\Courses\MaterialController as material;
use App\Http\Controllers\Backend\Quizzes\QuizController as quiz;
use App\Http\Controllers\Backend\Quizzes\QuestionController as question;
use App\Http\Controllers\Backend\Quizzes\OptionController as option;
use App\Http\Controllers\Backend\Quizzes\AnswerController as answer;
use App\Http\Controllers\Backend\Reviews\ReviewController as review;
use App\Http\Controllers\Backend\Communication\DiscussionController as discussion;
use App\Http\Controllers\Backend\Communication\MessageController as message;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchCourseController;
use App\Http\Controllers\CheckoutController as checkout;
use App\Http\Controllers\CouponController as coupon;

use App\Http\Controllers\Frontend\WatchCourseController as WatchCourseController;
use App\Http\Controllers\Frontend\LessonController as LessonController;
use App\Http\Controllers\LessonController as lesson;
use App\Http\Controllers\EnrollmentController as enrollment;
use App\Http\Controllers\EventSearchController;
use App\Http\Controllers\EventController as event;
use App\Models\Instructor;

/* students */
use App\Http\Controllers\Students\AuthController as sauth;
use App\Http\Controllers\Students\DashboardController as studashboard;
use App\Http\Controllers\Students\ProfileController as stu_profile;
use App\Http\Controllers\Students\sslController as sslcz;

use App\Http\Controllers\Frontend\QuizController;

/*
|--------------------------------------------------------------------------
| Debug Route
|--------------------------------------------------------------------------
*/
Route::get('/debug', function () {
    return response()->json([
        'php_version' => phpversion(),
        'laravel_version' => app()->version(),
        'env' => config('app.env'),
        'debug' => config('app.debug'),
        'db_connection' => config('database.default'),
    ]);
});

/*
|--------------------------------------------------------------------------
| Admin Auth Routes (No locale)
|--------------------------------------------------------------------------
*/
Route::get('/register', [auth::class, 'signUpForm'])->name('register');
Route::post('/register', [auth::class, 'signUpStore'])->name('register.store');
Route::get('/login', [auth::class, 'signInForm'])->name('login');
Route::post('/login', [auth::class, 'signInCheck'])->name('login.check');
Route::get('/logout', [auth::class, 'signOut'])->name('logOut');

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['checkauth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [dashboard::class, 'index'])->name('dashboard');
    Route::get('/userProfile', [auth::class, 'show'])->name('userProfile');
});

Route::middleware(['checkrole'])->prefix('admin')->group(function () {
    Route::resource('user', user::class);
    Route::resource('role', role::class);
    Route::resource('student', student::class);

    // Edit
    Route::get('instructor/{id}/edit', [InstructorController::class, 'edit'])
        ->name('instructor.edit');

    // Update
    Route::put('instructor/{id}', [InstructorController::class, 'update'])
        ->name('instructor.update');

    // Destroy
    Route::delete('instructor/{id}', [InstructorController::class, 'destroy'])
        ->name('instructor.destroy');

    // Остальные маршруты resource без edit/update/destroy
    Route::resource('instructor', InstructorController::class)
        ->except(['edit','update','destroy']);

    Route::resource('courseCategory', courseCategory::class);
    Route::resource('course', course::class);
    Route::get('/courseList', [course::class, 'indexForAdmin'])->name('courseList');
    Route::patch('/courseList/{update}', [course::class, 'updateforAdmin'])->name('course.updateforAdmin');
    Route::resource('material', material::class);
    Route::resource('lesson', lesson::class);
    Route::resource('event', event::class);
    Route::resource('quiz', quiz::class);
    Route::resource('question', question::class);
    Route::resource('option', option::class);
     Route::post('option/{option}/toggle-correctness', [option::class, 'toggleCorrectness'])
        ->name('option.toggle.correctness');
    Route::resource('answer', answer::class);
    Route::resource('review', review::class);
    Route::resource('discussion', discussion::class);
    Route::resource('message', message::class);
    Route::resource('coupon', coupon::class);
    Route::resource('enrollment', enrollment::class);
    Route::get('permission/{role}', [permission::class, 'index'])->name('permission.list');
    Route::post('permission/{role}', [permission::class, 'save'])->name('permission.save');
});

/*
|--------------------------------------------------------------------------
| Set Locale
|--------------------------------------------------------------------------
*/
Route::get('/set-locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ru', 'ka'])) {
        session(['locale' => $locale]);
        $previous = url()->previous();

        foreach (['en', 'ru', 'ka'] as $lang) {
            if (str_contains($previous, "/$lang/")) {
                return redirect(str_replace("/$lang/", "/$locale/", $previous));
            }
        }

        return redirect("/$locale");
    }

    return redirect()->back();
})->name('set.locale');

/*
|--------------------------------------------------------------------------
| Redirect root to default/current locale
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect(app()->getLocale());
});

/*
|--------------------------------------------------------------------------
| SSL Notify Routes (Non-localized)
|--------------------------------------------------------------------------
*/
// Убедитесь, что этот маршрут находится ВНЕ локализованной группы
Route::post('/ssl-payment-notify', [sslcz::class, 'notify'])->name('ssl.notify');

/*
|--------------------------------------------------------------------------
|--------------------------------------------------------------------------
| Localized Routes
|--------------------------------------------------------------------------
*/
Route::get('{locale}/event-search', [EventSearchController::class, 'index'])
    ->where('locale', 'en|ru|ka')
    ->name('event.search');

Route::prefix('{locale}')
    ->where(['locale' => 'en|ru|ka'])
    ->middleware('setlocale')
    ->group(function () {

        // Главная страница курса
        Route::get('/watch-course/{id}', [WatchCourseController::class, 'watchCourse'])
            ->name('frontend.watchCourse');

        // Маршруты для уроков и квизов
        Route::prefix('courses/{course}/lessons/{lesson}')->group(function () {

            // Просмотр урока
            Route::get('/', [LessonController::class, 'show'])
                ->name('lessons.show');

            // Прогресс урока
            Route::post('progress', [LessonController::class, 'updateProgress'])
                ->name('lessons.progress.update');

            // Завершение урока
            Route::post('complete', [LessonController::class, 'completeLesson'])
                ->name('lessons.complete');

            // Маршруты для квизов
            Route::prefix('quizzes/{quiz}')->group(function () {

                // Просмотр квиза
                Route::get('/', [QuizController::class, 'show'])
                    ->name('frontend.quizzes.show');

                // Начало квиза
                Route::post('/start', [QuizController::class, 'start'])
                    ->name('frontend.quizzes.start');

                // Прохождение квиза
                Route::get('/attempt/{attempt}', [QuizController::class, 'attempt'])
                    ->name('frontend.quizzes.attempt');

                // Отправка ответов
                Route::post('/attempt/{attempt}/submit', [QuizController::class, 'submitAttempt'])
                    ->name('frontend.quizzes.submit');

                // Результаты
                Route::get('/results/{attempt?}', [QuizController::class, 'results'])
                    ->name('frontend.quizzes.results');
            });
        });

        // Этот маршрут должен быть вне группы, так как он не соответствует префиксу
        Route::get('course/{id}/back', function ($locale, $id) {
            return redirect()->to("/$locale/watchCourse/$id");
        })->name('course.back');

        // 🔐 Student Auth
        Route::get('/student/register', [sauth::class, 'signUpForm'])->name('studentRegister');
        Route::post('/student/register', [sauth::class, 'signUpStore'])->name('studentRegister.store');

        Route::get('/student/login', [sauth::class, 'signInForm'])->name('studentLogin');
        Route::post('/student/login', [sauth::class, 'signInCheck'])->name('studentLogin.check');

        Route::get('/student/logout', [sauth::class, 'signOut'])->name('studentlogOut');

        // Protected Student Routes
        Route::middleware(['checkstudent'])->prefix('students')->group(function () {
            Route::get('/dashboard', [studashboard::class, 'index'])->name('studentdashboard');
            Route::get('/profile', [stu_profile::class, 'index'])->name('student_profile');
            Route::post('/profile/save', [stu_profile::class, 'save_profile'])->name('student_save_profile');
            Route::post('/profile/savePass', [stu_profile::class, 'change_password'])->name('change_password');
            Route::post('/change-image', [stu_profile::class, 'changeImage'])->name('change_image');

            // SSL Payment
            Route::post('/payment/ssl/submit', [sslcz::class, 'store'])->name('payment.ssl.submit');
        });

        // Frontend Routes
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/home', [HomeController::class, 'index']);
        Route::get('/searchCourse', [SearchCourseController::class, 'index'])->name('searchCourse');
        Route::get('/courseDetails/{id}', [course::class, 'frontShow'])->name('courseDetails');
        Route::get('/instructorProfile/{id}', [InstructorController::class, 'frontShow'])->name('instructorProfile');
        Route::get('/checkout', [checkout::class, 'index'])->name('checkout');
        Route::post('/checkout', [checkout::class, 'store'])->name('checkout.store');

        // 🛒 Cart
        Route::get('/cart_page', [CartController::class, 'index']);
        Route::get('/cart', [CartController::class, 'cart'])->name('cart');
        Route::get('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('add.to.cart');
        Route::patch('/update-cart', [CartController::class, 'update'])->name('update.cart');
        Route::delete('/remove-from-cart', [CartController::class, 'remove'])->name('remove.from.cart');
        Route::post('/coupon_check', [CartController::class, 'coupon_check'])->name('coupon_check');

        // 🧾 Static pages
        Route::get('/about', fn() => view('frontend.about'))->name('about');
        Route::get('/contact', fn() => view('frontend.contact'))->name('contact');
    }); // ЗАКРЫТИЕ ГРУППЫ ЛОКАЛИЗАЦИИ

/*
|--------------------------------------------------------------------------
| Для временного отключения аутентификации - добавьте этот middleware
|--------------------------------------------------------------------------
*/
// Временное решение: закомментируйте middleware проверки ролей
// или создайте временный middleware для отладки

Route::get('/check-courses', function () {
    $courses = \App\Models\Course::withCount('lessons')->get();

    return response()->json([
        'total_courses' => $courses->count(),
        'courses' => $courses->map(function($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'lessons_count' => $course->lessons_count,
                'has_quiz' => $course->lessons->contains('quiz_id', '!=', null)
            ];
        })
    ]);
});
