<?php

namespace App\View\Site\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render(): View
    {
        return view('site::pages.Layout.site');
    }
}
