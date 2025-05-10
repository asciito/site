<?php

namespace App\Site\View\Components;

use App\Settings\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function __construct(
        public $page = null,
        public bool $showFooter = true,
        public bool $shouldShowNavigation = true,
        public ?Settings $settings = null,
    ) {
        if (empty($this->settings)) {
            $this->settings = app(\App\AppSettings::class);
        }
    }

    public function render(): View
    {
        return view('site::pages.layout.site');
    }
}
