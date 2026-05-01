<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\SocialiteController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\CourseController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\InstructorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsInstructor;

// Auth Controllers for password reset and email verification
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/teach', function () {
    return redirect()->route('register');
})->name('instructor.landing');


// =============================
// AUTH (CUSTOM)
// =============================

Route::middleware('guest')->group(function () {

    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // OAuth Routes (Google & GitHub)
    Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
        ->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
        ->name('oauth.callback');
});


// =============================
// AUTHENTICATED USERS
// =============================

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email Verification
    Route::get('/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Password Confirmation
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Password Update
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{id}', [ProfileController::class, 'view'])->name('profile.view');

    // Default dashboard (fallback)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'instructor') {
            return redirect('/instructor/dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    // =============================
    // ADMIN PANEL
    // =============================
    Route::prefix('admin')
    ->middleware(['auth', 'is_admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
        Route::get('/enrollments', [AdminController::class, 'enrollments'])->name('enrollments');
        Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::get('/payouts', [AdminController::class, 'payouts'])->name('payouts');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });

    // =============================
    // INSTRUCTOR PANEL
    // =============================
    Route::middleware('is_instructor')
        ->prefix('instructor')
        ->name('instructor.')
        ->group(function () {
            Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
            Route::get('/courses', [InstructorController::class, 'indexCourses'])->name('courses.index');
            Route::get('/courses/create', [InstructorController::class, 'createCourse'])->name('courses.create');
            Route::get('/students', [InstructorController::class, 'indexStudents'])->name('students.index');
            Route::get('/reviews', [InstructorController::class, 'indexReviews'])->name('reviews.index');
            Route::get('/earnings', [InstructorController::class, 'indexEarnings'])->name('earnings.index');
            Route::get('/payouts', [InstructorController::class, 'indexPayouts'])->name('payouts.index');
            Route::get('/wallet', [InstructorController::class, 'indexWallet'])->name('wallet.index');
            Route::get('/profile', [InstructorController::class, 'indexProfile'])->name('profile.index');
            Route::get('/settings', [InstructorController::class, 'indexSettings'])->name('settings.index');
        });

});