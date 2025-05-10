<?php

namespace App\Site\View\Components;

use Illuminate\Contracts\View\View;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class ErrorLayout extends Layout
{
    public function __construct(public string $title, public int $code)
    {
        parent::__construct(
            page: new SEOData(robots: 'noindex, nofollow'),
            showFooter: false,
            shouldShowNavigation: false,
        );
    }

    public function render(): View
    {
        return view('site::pages.Layout.error');
    }
}
