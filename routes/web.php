<?php

use App\Http\Controllers\SiteController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::middleware(['track'])->group(function () {
    Route::get('/', [SiteController::class, 'index'])->name('home');
    Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
    Route::get('/terms-of-use', [SiteController::class, 'terms'])->name('terms');
    Route::get('/privacy-policy', [SiteController::class, 'privacy'])->name('privacy');

    Route::get('/{post:slug}', function (Post $post) {
        return view('site::pages.post', ['post' => $post]);
    })->name('post');
});
