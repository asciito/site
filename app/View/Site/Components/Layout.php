<?php

namespace App\View\Site\Components;

use App\Site\SiteSettings;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public SiteSettings $settings;

    public function __construct(public $page = null, public bool $showFooter = true)
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $this->settings = app(SiteSettings::class);
    }

    public function render(): View
    {
        return view('site::pages.Layout.site');
    }
}
