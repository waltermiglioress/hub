<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MngPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('mng')
            ->databaseNotifications()
            ->path('mng')
            ->login()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'green' => Color::Green,
                'estimated' => Color::Purple,
            ])
            ->brandName('Sicilsaldo Group')
//            ->brandLogo(fn () => view('filament.admin.logo'))

//            ->brandLogoHeight('8rem')
            ->favicon(asset('image/logo.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
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
            ])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::Full)
            ->navigationGroups([

                NavigationGroup::make()
                    ->label('Elenchi'),
                NavigationGroup::make()
                    ->label('Work'),
                NavigationGroup::make()
                    ->label('Setting'),
//                NavigationGroup::make()
//                    ->label(fn (): string => __('navigation.setting'))
//                    ->icon('heroicon-o-cog-6-tooth')
//                    ->collapsed(),
            ])
            ->navigationItems([
                NavigationItem::make('PowerBi')
                    ->url('https://app.powerbi.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-chart-pie')
                    ->sort(99)

                    //->group('Reports')
                    //->label(fn (): string => __('filament-panels::pages/dashboard.title'))
                    //->url(fn (): string => Dashboard::getUrl())
                    //->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard')),

            ])
            ->viteTheme('resources/css/filament/mng/theme.css')
            ->plugin(

                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 2
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),

            //FilamentSpatieRolesPermissionsPlugin::make()
            );
    }
}
