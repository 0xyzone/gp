<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\RecentOrdersTable;
use App\Filament\Widgets\TopSellingProductsChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Icons\Heroicon;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PanelPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('panel')
            ->path('/')
            ->viteTheme('resources/css/filament/panel/theme.css')
            ->login()
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2.25rem')
            ->colors([
                'primary' => [
                    50 => '#fff1f2',
                    100 => '#ffe4e6',
                    200 => '#fecdd3',
                    300 => '#fda4af',
                    400 => '#fb7185',
                    500 => '#e11d48',
                    600 => '#be123c',
                    700 => '#9f1239',
                    800 => '#881337', // Brand Burgundy
                    900 => '#4c0519',
                    950 => '#2d020e',
                ],
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full')
            ->navigationGroups([
                NavigationGroup::make('Expo POS'),
                NavigationGroup::make('Catalog'),
                NavigationGroup::make('Sales & Reports'),
            ])
            ->navigationItems([
                NavigationItem::make('Customer Display Screen')
                    ->url('/display', shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedTv)
                    ->group('Expo POS')
                    ->sort(2),
                NavigationItem::make('Kitchen & Orders Board')
                    ->url('/orders-board', shouldOpenInNewTab: true)
                    ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                    ->group('Expo POS')
                    ->sort(3),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                DashboardStatsOverview::class,
                TopSellingProductsChart::class,
                RecentOrdersTable::class,
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
}
