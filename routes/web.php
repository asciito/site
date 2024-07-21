<?php

use App\Http\Controllers\SiteController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('contact/', [SiteController::class, 'contact'])->name('contact');

Route::get('/{post:slug}', function (Post $post) {
    return view('site::pages.post', ['post' => $post]);
})->name('post');
