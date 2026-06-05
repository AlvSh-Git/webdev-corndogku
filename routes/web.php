<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\WhatsAppResetController;
use App\Http\Controllers\Customer\WelcomeController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\HistoryController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboard;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\Owner\ReportController;
use App\Http\Controllers\Owner\CategoryController;
use App\Http\Controllers\Owner\JadwalController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboard;
use App\Http\Controllers\Cashier\PurchaseController;
use App\Http\Controllers\Customer\ChatbotController;
use App\Http\Controllers\Owner\UserMaintenanceController;
use App\Http\Controllers\Customer\WishlistController;

// ── Public customer pages ───────────────────────────────────────
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/customize', [MenuController::class, 'customize'])->name('customize');
Route::get('/store-status', [MenuController::class, 'storeStatus'])->name('store.status');
Route::get('/api/products', [MenuController::class, 'catalog'])->name('api.products');
Route::get('/api/orders/{id}', [HistoryController::class, 'show'])->name('api.orders.show');
Route::post('/orders/{id}/send-whatsapp', [HistoryController::class, 'sendReceipt'])->name('orders.send-whatsapp');

// ── Chatbot ──────────────────────────────────────────────────────
Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])
    ->middleware('throttle:20,1')
    ->name('chatbot.send');

// ── Midtrans server-to-server payment notification (webhook) ─────
// Public + CSRF-exempt (see bootstrap/app.php); authenticated via signature_key.
Route::post('/midtrans/notification', [PurchaseController::class, 'midtransNotification'])->name('midtrans.notification');

// ── Cart ────────────────────────────────────────────────────────
Route::get('/cart',          [CartController::class, 'index'])->name('cart');
Route::post('/cart/add',     [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove',  [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update',  [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear',   [CartController::class, 'clear'])->name('cart.clear');

// ── Customer account area — requires login (any authenticated user) ──
Route::middleware('auth')->group(function () {
    // Checkout
    Route::get('/checkout',        [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/store', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/confirm', [CheckoutController::class, 'confirm'])->name('checkout.confirm');

    // Order history
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // Customer profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');

    // ── Wishlist Routes ──
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

});

// ── Auth ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:10,1');

    Route::get('auth/google',          [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);

    // ── Google SSO onboarding — complete profile for new users ──
    Route::get('/register/complete-profile',  [SocialiteController::class, 'showCompleteProfile'])->name('register.complete');
    Route::post('/register/complete-profile', [SocialiteController::class, 'completeProfile'])->name('register.complete.post')->middleware('throttle:10,1');

    // ── WhatsApp OTP password reset (Fonnte) ──
    Route::get('/forgot-password',           [WhatsAppResetController::class, 'show'])->name('password.wa.request');
    Route::post('/forgot-password/send-otp',   [WhatsAppResetController::class, 'sendOtp'])->name('password.wa.send')->middleware('throttle:5,1');
    Route::post('/forgot-password/verify-otp', [WhatsAppResetController::class, 'verifyOtp'])->name('password.wa.verify')->middleware('throttle:10,1');
    Route::post('/forgot-password/reset',      [WhatsAppResetController::class, 'resetPassword'])->name('password.wa.reset')->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Owner routes — management access (auth + owner role only) ───
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard',        [OwnerDashboard::class, 'index'])->name('dashboard');
    Route::get('/get-orders',          [OwnerDashboard::class, 'getOrders'])->name('get-orders');
    Route::get('/get-stats',           [OwnerDashboard::class, 'getStats'])->name('get-stats');
    Route::get('/get-chart-data',      [OwnerDashboard::class, 'getChartData'])->name('get-chart-data');
    Route::post('/store-status',       [OwnerDashboard::class, 'updateStatus'])->name('store.status');
    Route::post('/orders/{id}/status', [OwnerDashboard::class, 'updateOrderStatus'])->name('orders.status');

    Route::get('/products',              [ProductController::class, 'index'])->name('products');
    Route::post('/products',             [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}',    [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::post('/category/store',      [CategoryController::class, 'store'])->name('category.store');
    Route::post('/category/store-ajax', [CategoryController::class, 'storeAjax'])->name('category.storeAjax');

    // User Maintenance
    Route::get('/users', [UserMaintenanceController::class, 'index'])->name('users');
    Route::post('/users', [UserMaintenanceController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserMaintenanceController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserMaintenanceController::class, 'destroy'])->name('users.destroy');

    Route::get('/reports',                    [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/chart-data',         [ReportController::class, 'getChartData'])->name('reports.chart-data');
    Route::get('/reports/export',             [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/order/{id}/detail', [ReportController::class, 'orderDetail'])->name('reports.order.detail');

    Route::get('/jadwal-operasional',         [JadwalController::class, 'index'])->name('jadwal');
    Route::post('/jadwal-operasional',        [JadwalController::class, 'save'])->name('jadwal.save');
    Route::post('/jadwal-operasional/toggle', [JadwalController::class, 'toggleStatus'])->name('jadwal.toggle');
});

// ── Cashier routes — operational access (auth + cashier/owner) ──
Route::middleware(['auth', 'role:cashier,employee,owner'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/dashboard',           [CashierDashboard::class, 'index'])->name('dashboard');
    Route::get('/get-orders',          [CashierDashboard::class, 'getOrders'])->name('get-orders');
    Route::get('/get-stats',           [CashierDashboard::class, 'getStats'])->name('get-stats');
    Route::get('/get-chart-data',      [CashierDashboard::class, 'getChartData'])->name('get-chart-data');
    Route::post('/store-status',       [CashierDashboard::class, 'updateStatus'])->name('store.status');
    Route::post('/orders/{id}/status', [CashierDashboard::class, 'updateOrderStatus'])->name('orders.status');

    Route::get('/purchase',                         [PurchaseController::class, 'index'])->name('purchase');
    Route::get('/search-customer',                  [PurchaseController::class, 'searchCustomer'])->name('search-customer');
    Route::get('/get-products',                     [PurchaseController::class, 'getProducts'])->name('get-products');
    Route::post('/orders',                          [PurchaseController::class, 'store'])->name('orders.store');
    Route::post('/orders/{id}/send-whatsapp',       [PurchaseController::class, 'sendWhatsAppReceipt'])->name('orders.send-whatsapp');
    Route::post('/orders/{id}/mark-qris-paid',      [PurchaseController::class, 'markQrisPaid'])->name('orders.mark-qris-paid');
});
