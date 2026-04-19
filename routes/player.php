<?php
use App\Http\Controllers\Player\DashboardController;
use App\Http\Controllers\Player\ForgotProcessController;
use App\Http\Controllers\Player\PaymentController;
use App\Http\Controllers\Player\ProfileController;
use App\Http\Controllers\Player\PaymentSuccessController;
use App\Http\Controllers\Player\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/player/logout', [LoginController::class, 'logout'])->name('player.logout');

// --- Forgot Password Workflow ---
Route::get('/forgot-password', [ForgotProcessController::class, 'forgot'])->name('player.forgot');
Route::post('/forgot-password', [ForgotProcessController::class, 'forgot']);

// Create New Password (The link in the email leads here)
Route::get('/create-password', [ForgotProcessController::class, 'createPassword'])->name('player.create.password');
Route::post('/create-password', [ForgotProcessController::class, 'createPassword']);

// Protected Player Dashboard Routes
Route::middleware(['auth:player'])->prefix('player')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('player.index');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');

    Route::post('/payment', [PaymentController::class, 'payment'])->name('player.payment');
    Route::get('/payment/verify', [PaymentController::class, 'handlePaymentSuccess'])->name('player.payment.verify');
    Route::get('/payment/success', [PaymentSuccessController::class, 'success'])->name('player.payment.success');

});
