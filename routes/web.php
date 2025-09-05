<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Asset Management Routes
    Volt::route('assets', 'assets.index')->name('assets.index');
    Volt::route('assets/create', 'assets.create')->name('assets.create');
    Volt::route('assets/{asset}/edit', 'assets.edit')->name('assets.edit');
});

require __DIR__.'/auth.php';
