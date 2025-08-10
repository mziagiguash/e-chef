<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Setting\AuthenticationController as auth;
use App\Http\Controllers\Backend\Setting\UserController as user;
use App\Http\Controllers\Backend\Setting\DashboardController as dashboard;
use App\Http\Controllers\Backend\Setting\RoleController as role;
use App\Http\Controllers\Backend\Setting\PermissionController as permission;
use App\Http\Controllers\Backend\Students\StudentController as student;
use App\Http\Controllers\Backend\Instructors\InstructorController as instructor;
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
use App\Http\Controllers\WatchCourseController as watchCourse;
use App\Http\Controllers\LessonController as lesson;
use App\Http\Controllers\EnrollmentController as enrollment;
use App\Http\Controllers\EventController as event;

/* students */
use App\Http\Controllers\Students\AuthController as sauth;
use App\Http\Controllers\Students\DashboardController as studashboard;
use App\Http\Controllers\Students\ProfileController as stu_profile;
use App\Http\Controllers\Students\sslController as sslcz;

Route::get('/debug', function () {
    return response()->json([
        'php_version' => phpversion(),
        'laravel_version' => app()->version(),
        'env' => config('app.env'),
        'debug' => config('app.debug'),
        'db_connection' => config('database.default'),
    ]);
});

// 🔐 Auth routes
// ==========================
// 🔒 ADMIN routes (НЕ в locale)
Route::get('/register', [auth::class, 'signUpForm'])->name('register');
Route::post('/register', [auth::class, 'signUpStore'])->name('register.store');
Route::get('/login', [auth::class, 'signInForm'])->name('login');
Route::post('/login', [auth::class, 'signInCheck'])->name('login.check');
Route::get('/logout', [auth::class, 'signOut'])->name('logOut');


// ==========================
Route::middleware(['checkauth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [dashboard::class, 'index'])->name('dashboard');
    Route::get('userProfile', [auth::class, 'show'])->name('userProfile');
});

Route::middleware(['checkrole'])->prefix('admin')->group(function () {
    Route::resource('user', user::class);
    Route::resource('role', role::class);
    Route::resource('student', student::class);
    Route::resource('instructor', instructor::class);
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
    Route::resource('answer', answer::class);
    Route::resource('review', review::class);
    Route::resource('discussion', discussion::class);
    Route::resource('message', message::class);
    Route::resource('coupon', coupon::class);
    Route::resource('enrollment', enrollment::class);
    Route::get('permission/{role}', [permission::class, 'index'])->name('permission.list');
    Route::post('permission/{role}', [permission::class, 'save'])->name('permission.save');
});

// =============================
// 🌐 Set locale route
// =============================

// Установка локали
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

// Переадресация с корня на текущую/дефолтную локаль
Route::get('/', function () {
    return redirect(app()->getLocale());
});

// =============================
// 🌐 LOCALIZED ROUTES
// =============================
Route::prefix('{locale}')
    ->where(['locale' => 'en|ru|ka'])
    ->middleware('setlocale')
    ->group(function () {



        // 🔐 Student auth
        Route::get('/student/register', [sauth::class, 'signUpForm'])->name('studentRegister');
        Route::post('/student/register/{back_route}', [sauth::class, 'signUpStore'])->name('studentRegister.store');
        Route::get('/student/login', [sauth::class, 'signInForm'])->name('studentLogin');
        Route::post('/student/login/{back_route}', [sauth::class, 'signInCheck'])->name('studentLogin.check');
        Route::get('/student/logout', [sauth::class, 'signOut'])->name('studentlogOut');

        // 🏠 Frontend routes
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/home', [HomeController::class, 'index']);
        Route::get('/searchCourse', [SearchCourseController::class, 'index'])->name('searchCourse');
        Route::get('/courseDetails/{id}', [course::class, 'frontShow'])->name('courseDetails');
        Route::get('/watchCourse/{id}', [watchCourse::class, 'watchCourse'])->name('watchCourse');
        Route::get('/instructorProfile/{id}', [instructor::class, 'frontShow'])->name('instructorProfile');
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

        Route::middleware(['checkstudent'])->prefix('students')->group(function () {
    Route::get('/dashboard', [studashboard::class, 'index'])->name('studentdashboard');
    Route::get('/profile', [stu_profile::class, 'index'])->name('student_profile');
    Route::post('/profile/save', [stu_profile::class, 'save_profile'])->name('student_save_profile');
    Route::post('/profile/savePass', [stu_profile::class, 'change_password'])->name('change_password');
    Route::post('/change-image', [stu_profile::class, 'changeImage'])->name('change_image');

    // 💳 ssl payment
    Route::post('/payment/ssl/submit', [sslcz::class, 'store'])->name('payment.ssl.submit');

    });

// =============================
// 👩‍🎓 Student dashboard (non-localized)
// =============================
});

// 🌐 SSL notify routes
Route::post('/payment/ssl/notify', [sslcz::class, 'notify'])->name('payment.ssl.notify');
Route::post('/payment/ssl/cancel', [sslcz::class, 'cancel'])->name('payment.ssl.cancel');
