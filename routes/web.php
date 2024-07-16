<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');


Route::group([
    'as' => 'dashboard.',
    'prefix' => 'dashboard',
    'middleware' => ['auth', 'verified'],
], function () {
    Route::view('/', 'dashboard')->name('index');

    Route::view('profile/', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
