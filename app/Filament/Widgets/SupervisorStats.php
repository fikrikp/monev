<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceReq;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\HtmlString;

class SupervisorStats extends StatsOverviewWidget
{

    public static function canView(): bool
    {
        return \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'supervisor';
    }

    protected function getCards(): array
    {
        return [
            // ✅ PERBAIKAN: Gunakan 'our_purchasing' tanpa spasi
            Card::make('Our Purchasing', MaintenanceReq::where('status', 'our_purchasing')->count())
                ->icon('heroicon-o-shopping-cart') // ikon bisa diganti sesuai tema
                ->color('info'),

            // ✅ PERBAIKAN: Gunakan 'waiting_material' tanpa spasi
            Card::make('Waiting Material', MaintenanceReq::where('status', 'waiting_material')->count())
                ->icon('heroicon-o-clock') // ikon pending
                ->color('warning'),

            // 'in_progress' sudah benar
            Card::make('In Progress', MaintenanceReq::where('status', 'in_progress')->count())
                ->icon('heroicon-o-cog')
                ->color('success'),
        ];
    }
}
