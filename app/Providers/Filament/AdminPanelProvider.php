<?php

namespace App\Providers\Filament;

use App\Filament\Pages\RecruitmentDashboard;
use App\Filament\Widgets\ApplicantsByStatusChart;
use App\Filament\Widgets\ApplicantsPerJob;
use App\Filament\Widgets\RecentApplicants;
use App\Filament\Widgets\RecruitmentStatsOverview;
use App\Filament\Widgets\RecruitmentWelcome;
use App\Http\Middleware\NoIndex;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName('HSB Recruitment')
            ->favicon(asset('favicon.svg'))
            ->colors([
                'primary' => Color::hex('#1b39e6'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                RecruitmentDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                RecruitmentWelcome::class,
                RecruitmentStatsOverview::class,
                RecentApplicants::class,
                ApplicantsByStatusChart::class,
                ApplicantsPerJob::class,
            ])
            ->middleware([
                NoIndex::class,
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
