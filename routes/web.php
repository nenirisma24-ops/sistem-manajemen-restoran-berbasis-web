<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerOrderController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (Bisa diakses oleh semua role)
    Route::view('dashboard', 'dashboard')->name('dashboard')->middleware('role:admin,kasir,pelayan,customer');

    // Rute Admin
    Route::middleware('role:admin')->group(function () {
        Route::livewire('/categories', 'pages::category.index')->name('category.index');
    });

    // Rute Admin & Customer (Menu)
    Route::middleware('role:admin,customer')->group(function () {
        Route::livewire('/menus', 'pages::menu.index')->name('menu.index');
    });

    // Rute Customer (Pemesanan spesifik)
    Route::middleware('role:customer')->group(function () {
        Route::get('/customer/orders', [CustomerOrderController::class, 'index'])->name('customer.orders');
        Route::post('/customer/orders', [CustomerOrderController::class, 'store'])->name('customer.orders.store');
    });

    // Rute Pesanans (Pusat data pesanan untuk semua role)
    Route::middleware('role:admin,kasir,pelayan,koki,customer')->group(function () {
        Route::livewire('/pesanans', 'pages::pesanan.index')->name('pesanan.index');
    });

    // Rute Order Baru & Payments
    Route::middleware('role:admin,kasir,customer')->group(function () {
        Route::livewire('/payments', 'pages::payment.index')->name('payment.index');
        Route::livewire('/order-baru', 'pages::order.create')->name('order.create');
    });

    // Rute Pelayan
    Route::middleware('role:admin,pelayan')->group(function () {
        Route::livewire('/tables', 'pages::table.index')->name('table.index');
        Route::livewire('/detail-pesanan', 'pages::detail_pesanan.index')->name('detail-pesanan.index');
    });

    // Rute Gudang
    Route::middleware('role:gudang')->group(function () {
        Route::view('/inventory', 'inventory')->name('inventory');
    });
});

require __DIR__.'/settings.php';