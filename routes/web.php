<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [\App\Http\Controllers\SiteController::class, 'index'])
        ->name('home');
    \Livewire\Volt\Volt::route('contact/', 'contact')
        ->name('contact');

    Route::get('/{post:slug}', function (\App\Models\Post $post) {
       return  view('site::pages.post', ['post' => $post]);
    })->name('post');
});

Route::group([
    'as' => 'dashboard.',
    'prefix' => 'dashboard',
    'middleware' => ['auth', 'verified'],
], function () {
    Route::view('/', 'dashboard')->name('index');

    Route::view('profile/', 'profile')->name('profile');
});
