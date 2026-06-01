<?php

namespace App\Providers\Filament;

use App\Models\SiteSetting;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use MWGuerra\FileManager\FileManagerPlugin;
use MWGuerra\FileManager\Filament\Pages\FileManager;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('JobList')
            ->brandLogo(fn(): ?string => SiteSetting::current()?->headerLogoUrl())
            ->brandLogoHeight('2rem')
            ->favicon(fn(): ?string => SiteSetting::current()?->faviconUrl())
            ->darkMode()
            ->defaultThemeMode(ThemeMode::Light)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->collapsedSidebarWidth('4.25rem')
            ->theme($this->adminTheme())
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn(): string => view('filament.hooks.footer', [
                    'settings' => SiteSetting::current(),
                ])->render(),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\Filament\Clusters')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()->label('Recruitment'),
                NavigationGroup::make()->label('Administration'),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugins([
                FileManagerPlugin::make()
                    ->only([
                        FileManager::class,
                    ])
                    ->fileManagerNavigation(
                        label: 'File Manager',
                        group: 'Administration',
                    )
                    ->withoutSchemaExample(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function adminTheme(): HtmlString
    {
        $path = public_path('css/admin-panel.css');
        $version = file_exists($path) ? filemtime($path) : time();

        return new HtmlString('<link href="' . asset('css/admin-panel.css') . '?v=' . $version . '" rel="stylesheet" data-navigate-track />');
    }
}
