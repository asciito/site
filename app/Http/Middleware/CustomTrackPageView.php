<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pirsch\Http\Middleware\TrackPageview;
use Symfony\Component\HttpFoundation\Response;

class CustomTrackPageView extends TrackPageview
{
    public function handle(Request $request, Closure $next, string ...$excepts): Response
    {
        if (! Auth::check() && (app()->isProduction() || ! app()->runningUnitTests() || ! app()->runningInConsole())) {
            return parent::handle($request, $next, ...$excepts);
        }

            return $next($request, $excepts);
    }
}
