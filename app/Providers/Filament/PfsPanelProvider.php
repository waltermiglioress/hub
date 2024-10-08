<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\PfsLogin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PfsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pfs')
            ->path('pfs')
            ->authGuard('pfs')
            ->login(PfsLogin::class)
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Purple,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'green' => Color::Green,
                'estimated' => Color::Purple,
            ])
            ->brandName(' PFS - Sicilsaldo Group')
            ->discoverResources(in: app_path('Filament/Pfs/Resources'), for: 'App\\Filament\\Pfs\\Resources')
            ->discoverPages(in: app_path('Filament/Pfs/Pages'), for: 'App\\Filament\\Pfs\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Pfs/Widgets'), for: 'App\\Filament\\Pfs\\Widgets')
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
            ->collapsedSidebarWidth('9rem')
            ->maxContentWidth(MaxWidth::Full)
            ->viteTheme('resources/css/filament/pfs/theme.css')
//            ->plugin(
//                FilamentShieldPlugin::make()
//                    ->gridColumns([
//                        'default' => 1,
//                        'sm' => 2,
//                        'lg' => 2
//                    ])
//                    ->sectionColumnSpan(1)
//                    ->checkboxListColumns([
//                        'default' => 1,
//                        'sm' => 2,
//                        'lg' => 3,
//                    ])
//                    ->resourceCheckboxListColumns([
//                        'default' => 1,
//                        'sm' => 2,
//                    ]),
//            )
            ;
    }
}
