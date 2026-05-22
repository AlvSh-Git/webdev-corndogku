<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\CategoryController;

// ── Public customer catalog ─────────────────────────────────────
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/menu', function () {
    $storeInfo = (new \App\Http\Controllers\JadwalController)->statusInfo();
    return view('menu.index', compact('storeInfo'));
})->name('menu');
Route::get('/customize', fn () => view('customize'))->name('customize');
Route::get('/store-status', [\App\Http\Controllers\JadwalController::class, 'getStatus'])->name('store.status');
Route::get('/api/products', [ProductController::class, 'catalog'])->name('api.products');

// ── Cart ────────────────────────────────────────────────────────
Route::get('/cart',          [CartController::class, 'index'])->name('cart');
Route::post('/cart/add',     [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove',  [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear',   [CartController::class, 'clear'])->name('cart.clear');

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
    Route::get('/dashboard',        [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/get-orders',       [DashboardController::class, 'getOrders'])->name('get-orders');
    Route::get('/get-stats',        [DashboardController::class, 'getStats'])->name('get-stats');
    Route::post('/store-status',    [DashboardController::class, 'updateStatus'])->name('store.status');
    Route::get('/products',              [ProductController::class,   'index'])->name('products');
    Route::post('/products',             [ProductController::class,   'store'])->name('products.store');
    Route::put('/products/{product}',    [ProductController::class,   'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class,   'destroy'])->name('products.destroy');
    Route::post('/category/store',       [CategoryController::class,  'store'])->name('category.store');
    Route::post('/category/store-ajax',  [CategoryController::class,  'storeAjax'])->name('category.storeAjax');
    Route::get('/users',                 [UserController::class,      'index'])->name('users');
    Route::get('/reports',                          [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/order/{id}/detail',       [ReportController::class, 'orderDetail'])->name('reports.order.detail');
    Route::get('/jadwal-operasional',    [JadwalController::class, 'index'])->name('jadwal');
    Route::post('/jadwal-operasional',   [JadwalController::class, 'save'])->name('jadwal.save');
    Route::post('/jadwal-operasional/toggle', [JadwalController::class, 'toggleStatus'])->name('jadwal.toggle');
});

// ── Cashier routes — operational access ─────────────────────────
Route::prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard',              [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/get-orders',             [DashboardController::class, 'getOrders'])->name('get-orders');
    Route::get('/get-stats',              [DashboardController::class, 'getStats'])->name('get-stats');
    Route::post('/store-status',          [DashboardController::class, 'updateStatus'])->name('store.status');
    Route::post('/orders/{id}/status',    [DashboardController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('/purchase',               [PurchaseController::class,  'index'])->name('purchase');
    Route::get('/search-customer',        [PurchaseController::class,  'searchCustomer'])->name('search-customer');
    Route::get('/get-products',           [PurchaseController::class,  'getProducts'])->name('get-products');
    Route::post('/orders',                [PurchaseController::class,  'store'])->name('orders.store');
});
