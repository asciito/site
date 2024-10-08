<?php

namespace App\Providers\Filament;

use App\Site;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class WebtoolsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = app(Site\SiteSettings::class);

        try {
            $panel->brandName($settings->site_name);

            $this->setDefaultSiteImage($settings->site_image);
        } catch (QueryException) {
            // The table does not exist
        }

        return $panel
            ->default()
            ->id('webtools')
            ->path(config('site.webtools_path'))
            ->login()
            ->colors([
                'primary' => '#0000AA',
                'secondary' => '#33FF33',
                'success' => '#33FF33',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Site\Filament\Pages\SiteSettingsPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn () => Blade::render(<<<'HTML'
                <x-filament::link
                    :href="route('home')"
                    icon="heroicon-s-globe-alt"
                    class="cursor-pointer"
                >
                    Site
                </x-filament::link>
            HTML)
        );
    }

    protected function setDefaultSiteImage(?string $url): void
    {
        app('config')->set(['seo.image.fallback' => $url]);
    }
}
