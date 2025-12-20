<?php

// routes/web.php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- Application Pages ---

// Route::get('/', function () {
//     return Inertia::render('Index');
// });

Route::get('/', function () {
    return Inertia::render('join/Index');
});

// Route::get('/sample', function () {
//     return Inertia::render('auth/registration/sample');
// });

// Route::get('/privacy-policy', function () {
//     return Inertia::render('privacy-policy/Index');
// });

require __DIR__ .'/auth.php';
require __DIR__ .'/admin.php';
