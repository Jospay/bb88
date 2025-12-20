<?php

use App\Http\Controllers\Admin\AllocationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EarningController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\LoginController; // You will create this
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public login routes
// 1. Show the Page
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
// 2. Handle the Form Submission
Route::post('/admin/login', [LoginController::class, 'login']);
// 3. Handle Logout
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

// Protected Admin Dashboard Routes
Route::middleware(['auth:admin'])->prefix('admin')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('admin.index');
    Route::get('/users', [UsersController::class, 'index'])->name('users');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');

    // Allocation
    Route::get('/allocation', [AllocationController::class, 'index'])->name('admin.allocation.index');
    Route::post('/allocation', [AllocationController::class, 'store'])->name('admin.allocation.store');
    Route::put('/allocation/{allocation}', [AllocationController::class, 'update'])->name('admin.allocation.update');
    Route::delete('/allocation/{allocation}', [AllocationController::class, 'destroy'])->name('admin.allocation.destroy');

    // Earning
    Route::get('/earning', [EarningController::class, 'index'])->name('admin.earning');
    Route::get('/earning/export/{format}', [EarningController::class, 'export'])->name('admin.earning.export');
});
