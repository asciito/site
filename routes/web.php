<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('/', [\App\Http\Controllers\SiteController::class, 'index']);
    Route::get('contact/', [App\Http\Controllers\SiteController::class, 'contact']);
});

Route::group([
    'as' => 'dashboard.',
    'prefix' => 'dashboard',
    'middleware' => ['auth', 'verified'],
], function () {
    Route::view('/', 'dashboard')->name('index');

    Route::view('profile/', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
