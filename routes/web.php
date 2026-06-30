<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/categories', 'pages::category.index')->name('category.index');
    Route::livewire('/menus', 'pages::menu.index')->name('menu.index');
    Route::livewire('/detail-pesanan', 'pages::detail_pesanan.index')->name('detail-pesanan.index');
    Route::livewire('/tables', 'pages::table.index')->name('table.index');
    Route::livewire('/pesanans', 'pages::pesanan.index')->name('pesanan.index');
});

require __DIR__.'/settings.php';
