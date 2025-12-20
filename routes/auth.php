<?php
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// GET route to serve the registration form page
Route::get('/register', function () {
    return Inertia::render('auth/registration/Index', [
        'flash' => session()->only(['status', 'error']),
    ]);
});

// Route::get('/admin/login', function () {
//     return Inertia::render('auth/login/Index');
// });

// --- API Endpoint for Registration ---
Route::post('/api/register', [RegistrationController::class, 'register']);

// 1. PayMongo Success URL (Verification Handler)
Route::get('/payment/verify', [RegistrationController::class, 'handlePaymentSuccess']);

// 2. Final Success Page (Inertia Renderer)
Route::get('/payment/success', [PaymentController::class, 'success']);
