<?php

namespace App\View\Site\Components;

use App\Site\SiteSettings;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function __construct(
        public $page = null,
        public bool $showFooter = true,
        public ?SiteSettings $settings = null)
    {
        if (empty($this->settings)) {
            $this->settings = app(SiteSettings::class);
        }
    }

    public function render(): View
    {
        return view('site::pages.Layout.site');
    }
}
