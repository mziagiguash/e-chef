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
use App\Http\Controllers\Frontend\CourseDetailController;

use App\Http\Controllers\Frontend\LessonController as LessonController;
use App\Http\Controllers\Backend\LessonController as lesson;
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
use App\Http\Controllers\Frontend\FrontendInstructorController;

/*
|--------------------------------------------------------------------------
| Debug Route
|--------------------------------------------------------------------------
*/
Route::get('/debug-course/{id}', function($id) {
    try {
        $course = \App\Models\Course::find($id);
        if ($course) {
            return response()->json([
                'exists' => true,
                'title' => $course->title,
                'status' => $course->status
            ]);
        } else {
            return response()->json(['exists' => false]);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
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
    Route::get('courses', [course::class, 'indexForAdmin'])->name('courses.index');
    Route::resource('courses', course::class); // ✅ Правильно
    Route::resource('courses', course::class)->except(['index']);
    Route::resource('material', material::class);
    Route::resource('lesson', lesson::class);
    Route::resource('event', event::class);
    Route::resource('quiz', quiz::class);
    Route::resource('question', question::class);
    Route::resource('option', option::class);
    Route::resource('answer', answer::class);
    Route::resource('review', review::class);
    Route::resource('discussion', discussion::class);
    Route::resource('message', message::class);
    Route::resource('coupon', coupon::class);
    Route::resource('enrollment', enrollment::class);
    Route::get('/enrollment/statistics', [enrollment::class, 'statistics'])->name('enrollment.statistics');
    Route::post('/enrollment/{enrollment}/update-payment-status', [enrollment::class, 'updatePaymentStatus'])->name('enrollment.update-payment-status');
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
| SSL Payment Routes (Non-localized) - ВНЕ локализованной группы
|--------------------------------------------------------------------------
*/
Route::post('/ssl-payment-notify', [sslcz::class, 'notify'])->name('ssl.notify');
Route::post('/payment/ssl/notify', [sslcz::class, 'notify'])->name('payment.ssl.notify');
Route::post('/payment/ssl/cancel', [sslcz::class, 'cancel'])->name('payment.ssl.cancel');

/*
|--------------------------------------------------------------------------
| Localized Routes
|--------------------------------------------------------------------------
*/
Route::prefix('{locale}')
    ->where(['locale' => 'en|ru|ka'])
    ->middleware('setlocale')
    ->group(function () {

        // Главная страница
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/home', [HomeController::class, 'index']);

        // 🔐 Student Auth Routes - ДОБАВЛЕНЫ ПРОПУЩЕННЫЕ МАРШРУТЫ
        Route::get('/student/register', [sauth::class, 'signUpForm'])->name('studentRegister');
        Route::post('/student/register/{back_route?}', [sauth::class, 'signUpStore'])->name('studentRegister.store');
        Route::get('/student/login', [sauth::class, 'signInForm'])->name('studentLogin');
        Route::post('/student/login/{back_route?}', [sauth::class, 'signInCheck'])->name('studentLogin.check');
        Route::get('/student/logout', [sauth::class, 'signOut'])->name('studentlogOut');

        // Protected Student Routes
        Route::middleware(['checkstudent'])->prefix('students')->group(function () {
            Route::get('/dashboard', [studashboard::class, 'index'])->name('studentdashboard');
            Route::get('/profile', [stu_profile::class, 'index'])->name('student_profile');
            Route::post('/profile/save', [stu_profile::class, 'save_profile'])->name('student_save_profile');
            Route::post('/profile/savePass', [stu_profile::class, 'change_password'])->name('change_password');
            Route::post('/change-image', [stu_profile::class, 'changeImage'])->name('change_image');

            // SSL Payment Routes - ВНУТРИ защищенной группы студентов
            Route::post('/payment/ssl/submit', [sslcz::class, 'store'])->name('payment.ssl.submit');
        });

        // Маршруты инструкторов
        Route::get('/instructors', [FrontendInstructorController::class, 'index'])->name('frontend.instructors');
        Route::get('/instructor/{id}', [FrontendInstructorController::class, 'show'])->name('frontend.instructor.show');


        // Курсы
        Route::get('/courses', [WatchCourseController::class, 'index'])->name('frontend.courses');
        Route::get('/courses/{course}', [WatchCourseController::class, 'show'])->name('frontend.courses.show');

        // 🛒 Cart Routes
Route::get('/cart', [CartController::class, 'cart'])->name('cart');
Route::get('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('add.to.cart');
Route::patch('/update-cart', [CartController::class, 'update'])->name('update.cart');
Route::delete('/remove-from-cart', [CartController::class, 'remove'])->name('remove.from.cart');
Route::post('/coupon-check', [coupon::class, 'coupon_check'])->name('coupon.check'); // Исправлено имя
Route::post('/coupon-remove', [coupon::class, 'remove_coupon'])->name('coupon.remove'); // Добавьте этот маршрут

// Checkout

Route::get('/checkout', [checkout::class, 'index'])->name('checkout');
Route::post('/checkout', [checkout::class, 'store'])->name('checkout.store');
        // Поиск
        Route::get('/searchCourse', [SearchCourseController::class, 'index'])->name('searchCourse');
        Route::get('/event-search', [EventSearchController::class, 'index'])->name('event.search');

        // Маршруты для уроков и квизов
        Route::prefix('courses/{course}/lessons/{lesson}')->group(function () {
            Route::get('/', [LessonController::class, 'show'])->name('lessons.show');

            Route::prefix('quizzes/{quiz}')->group(function () {
                Route::get('/', [QuizController::class, 'show'])->name('frontend.quizzes.show')->scopeBindings();
                Route::post('/start', [QuizController::class, 'start'])->name('frontend.quizzes.start');
                Route::get('/attempt/{attempt}', [QuizController::class, 'attempt'])->name('frontend.quizzes.attempt');
                Route::post('/attempt/{attempt}/submit', [QuizController::class, 'submit'])->name('frontend.quizzes.submit');
                Route::get('/results/{attempt}', [QuizController::class, 'results'])->name('frontend.quizzes.results');
            });
        });

        // Редирект
        Route::get('courses/{id}/back', function ($locale, $id) {
            return redirect()->to("/$locale/courses/$id");
        })->name('course.back');

        // Детали курса
        Route::get('/courseDetails/{id}', [course::class, 'frontShow'])->name('courseDetails');
        Route::get('/instructorProfile/{id}', [InstructorController::class, 'frontShow'])->name('instructorProfile');

        // 🧾 Static pages
        Route::get('/about', fn() => view('frontend.about'))->name('about');
        Route::get('/contact', fn() => view('frontend.contact'))->name('contact');
    });

/*
|--------------------------------------------------------------------------
| Debug Route
|--------------------------------------------------------------------------
*/
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
