<?php

use App\Blog\Models\Post;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['track'])->group(function () {
    Route::get('/', [SiteController::class, 'index'])->name('home');
    Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
    Route::get('/terms-of-use', [SiteController::class, 'terms'])->name('terms');
    Route::get('/privacy-policy', [SiteController::class, 'privacy'])->name('privacy');

    Route::get('/{post:slug}', fn (Post $post) => view('blog::pages.post', ['post' => $post]))->name('post');
});
