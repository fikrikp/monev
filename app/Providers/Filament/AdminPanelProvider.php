<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset; // Pastikan ini diimpor
use Filament\Support\Assets\Js;
use App\Filament\Pages\Auth\Login; // ✅ PENTING: Pastikan ini diimpor


use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Import semua Resource yang Anda gunakan
use App\Filament\Resources\UserResource;
use App\Filament\Resources\RoomResource;
use App\Filament\Resources\BarangResource;
use App\Filament\Resources\MaintenanceReqResource;
use App\Filament\Resources\ReportResource;
use App\Filament\Pages\Dashboard;


class AdminPanelProvider extends PanelProvider
{


    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('favicon.ico'))
            ->brandName('')
            ->colors([
                'primary' => '#ff6700',
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                UserResource::class,
                RoomResource::class,
                BarangResource::class,
                MaintenanceReqResource::class,
                ReportResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages(array_filter([
                Dashboard::class,
            ]))
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Biarkan array ini kosong, karena widget diatur di Dashboard.php
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
}
