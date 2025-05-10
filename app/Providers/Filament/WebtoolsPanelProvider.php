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
        return $panel
            ->brandName(function (Site\SiteSettings $settings) {
                return $settings->name;
            })
            ->profile(Site\Filament\Pages\ProfilePage::class)
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label('Settings')
                    ->url(fn () => Site\Filament\Pages\SiteSettingsPage::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
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
            ->discoverResources(in: app_path('Blog/Filament/Resources'), for: 'App\\Blog\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverPages(in: app_path('Site/Filament/Pages'), for: 'App\\Site\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
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
}
