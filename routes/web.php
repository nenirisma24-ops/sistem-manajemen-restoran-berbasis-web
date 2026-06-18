<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('/categories', 'pages::category.index')->name('category.index');
    Route::livewire('/menus', 'pages::menu.index')->name('menu.index');
});

require __DIR__.'/settings.php';
