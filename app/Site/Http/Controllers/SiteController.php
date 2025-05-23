<?php

namespace App\Site\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(): Request|View
    {
        return view('site::pages.home', [
            'user' => User::first(),
        ]);
    }

    public function contact(): Request|View
    {
        return view('site::pages.contact');
    }

    public function terms(): Request|View
    {
        return view('site::pages.terms');
    }

    public function privacy(): Request|View
    {
        return view('site::pages.privacy');
    }
}
