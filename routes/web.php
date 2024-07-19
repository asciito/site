<?php

use App\Http\Controllers\SiteController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [SiteController::class, 'index'])->name('home');
    Volt::route('contact/', 'contact')->name('contact');

    Route::get('/{post:slug}', function (Post $post) {
       return  view('site::pages.post', ['post' => $post]);
    })->name('post');
});
