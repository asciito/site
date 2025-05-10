<?php

namespace App\Site\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class ErrorLayout extends Component
{
    public function __construct(public string $title, public int $code)
    {
        //
    }

    public function render(): View
    {
        return view('site::pages.Layout.error', [
            'page' => new SEOData(
                robots: 'noindex, nofollow',
            )
        ]);
    }
}
