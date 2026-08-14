<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProductController;
use App\Livewire\Storefront\Cart;
use App\Livewire\Storefront\Catalog;
use App\Livewire\Storefront\Checkout;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/shop', Catalog::class)->name('shop');
Route::get('/sneakers/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/bag', Cart::class)->name('cart');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/orders/{order:public_id}/received', function (Order $order) {
    return view('storefront.order-success', compact('order'));
})->name('checkout.success');
Route::view('/offline', 'storefront.offline')->name('offline');

Route::get('/sitemap.xml', [MarketingController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [MarketingController::class, 'robots'])->name('robots');
Route::get('/feeds/meta-products.csv', [MarketingController::class, 'metaFeed'])->name('feeds.meta');

Route::middleware('guest')->group(function (): void {
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::post('logout', [AdminAuthController::class, 'destroy'])->name('logout');
});
