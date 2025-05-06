<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Routes for guests (Login, Forgot Password, Reset Password)
// These should *not* require admin role
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');
});

// Routes that require authentication (e.g., Email Verification, Confirm Password, Logout)
// These do NOT require admin role, just being logged in
Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmailController::class)
                ->middleware('throttle:6,1')
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});


// !!! NEW GROUP FOR ADMIN-ONLY ROUTES (REGISTRATION) !!!
// These routes require the user to be AUTHENTICATED *AND* pass the ADMIN middleware check
Route::middleware(['auth', 'admin'])->group(function () { // Require both 'auth' and 'admin'
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register'); // Name the route 'register'

    Route::post('register', [RegisteredUserController::class, 'store']); // Handles form submission
});
