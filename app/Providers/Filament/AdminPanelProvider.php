<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Backups;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Http\Middleware\TrackLastLoginMiddleware;
use App\Models\Team;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Firefly\FilamentBlog\Blog;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Joaopaulolndev\FilamentGeneralSettings\FilamentGeneralSettingsPlugin;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;
use Rmsramos\Activitylog\ActivitylogPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->profile()
            ->path('admin')
            ->login()
            ->registration()
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'primary' => Color::Amber,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->font('Inter', provider: GoogleFontProvider::class)
            ->brandName('Filament Fun')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
                TrackLastLoginMiddleware::class,
            ])
            ->spa()
            ->plugins(plugins: [
                FilamentShieldPlugin::make(),
                BreezyCore::make()
                    ->myProfile(
                        slug: 'profile',
                        navigationGroup: 'Settings'
                    ),
                FilamentShieldPlugin::make(),
                FilamentApexChartsPlugin::make(),
                FilamentGeneralSettingsPlugin::make(),
                SpotlightPlugin::make(),
                ActivitylogPlugin::make()
                    ->navigationGroup('General Settings'),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Backups::class),
                Blog::make(),
            ])
            ->databaseNotifications()
          //  ->tenant(model: Team::class, slugAttribute: 'slug', ownershipRelationship: 'team')
           // ->tenantRegistration(RegisterTeam::class)
          /*  ->tenantMiddleware([
                \BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant::class,
            ], isPersistent: true)*/
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
