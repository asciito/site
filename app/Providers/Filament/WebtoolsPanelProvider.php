<?php

namespace App\Providers\Filament;

use App\Site\Filament\Pages\ProfilePage;
use App\Site\Filament\Pages\SiteSettings;
use App\Site\Settings\SiteSettings as Settings;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
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
            ->darkMode(false)
            ->brandName(fn (Settings $settings) => $settings->name)
            ->profile(ProfilePage::class)
            ->userMenuItems([
                Action::make('settings-page')
                    ->label('Settings')
                    ->url(fn () => SiteSettings::getUrl())
                    ->icon(SiteSettings::getNavigationIcon()),
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
            ->discoverResources(in: app_path('Site/Filament/Resources'), for: 'App\\Site\\Filament\\Resources')
            ->discoverResources(in: app_path('Blog/Filament/Resources'), for: 'App\\Blog\\Filament\\Resources')
            ->discoverPages(in: app_path('Site/Filament/Pages'), for: 'App\\Site\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
