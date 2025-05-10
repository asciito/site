<?php

namespace App\View\Site\Components;

use App\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function __construct(
        public $page = null,
        public bool $showFooter = true,
        public ?Settings $settings = null,
        public bool $shouldShowNavigation = true,
    ) {
        if (empty($this->settings)) {
            $this->settings = app(Settings::class);
        }
    }

    public function render(): View
    {
        return view('site::pages.Layout.site');
    }
}
