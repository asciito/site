<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pirsch\Http\Middleware\TrackPageview;
use Symfony\Component\HttpFoundation\Response;

class CustomTrackPageView extends TrackPageview
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() || ! app()->isProduction()) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
