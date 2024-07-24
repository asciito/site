<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(): Request|View
    {
        return view('site::pages.home');
    }

    public function contact(): Request|View
    {
        return view('site::pages.contact');
    }
}
