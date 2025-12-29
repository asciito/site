<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Configuration;
use App\Filament\Pages\ProfilePage;
use App\Settings\SiteSettings;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Override;

class WebtoolsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->favicon(fn (SiteSettings $settings) => $settings->favicon ? Storage::url($settings->favicon) : null)
            ->darkMode(false)
            ->brandName(fn (SiteSettings $settings) => $settings->name)
            ->profile(ProfilePage::class)
            ->userMenuItems([
                Action::make('settings-page')
                    ->label('Settings')
                    ->url(fn () => Configuration::getUrl())
                    ->icon(Configuration::getNavigationIcon()),
            ])
            ->default()
            ->id('webtools')
            ->path(config('site.webtools_path'))
            ->login()
            ->colors([
                'primary' => '#33ff33',
                'secondary' => '#0000aa',
                'success' => '#5cff5c',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
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

    #[Override]
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
                    color="secondary"
                >
                    <strong>Site</strong>
                </x-filament::link>
            HTML)
        );
    }
}
