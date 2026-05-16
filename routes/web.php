<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

// ── Public customer catalog ─────────────────────────────────────
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/menu',      fn () => view('menu.index'))->name('menu');
Route::get('/cart',      fn () => view('cart.index'))->name('cart');
Route::get('/customize', fn () => view('customize'))->name('customize');

// ── Customer profile ────────────────────────────────────────────
Route::get('/profile', function () {
    $user = auth()->user() ?? (object)[
        'name'     => 'Casey',
        'username' => 'caseydw',
        'email'    => 'caseydw00@gmail.com',
        'phone'    => '082189134241',
        'role'     => 'customer',
        'branch'   => null,
        'status'   => 'active',
    ];
    return view('profile.index', compact('user'));
})->name('profile');

// ── Auth ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', fn () => back())->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Owner routes — management access ────────────────────────────
Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products',  [ProductController::class,   'index'])->name('products');
    Route::get('/users',     [UserController::class,      'index'])->name('users');
    Route::get('/reports',   [ReportController::class, 'index'])->name('reports');
});

// ── Cashier routes — operational access ─────────────────────────
Route::prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/purchase',  [PurchaseController::class,  'index'])->name('purchase');
});
