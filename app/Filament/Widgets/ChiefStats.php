<?php

namespace App\Filament\Widgets;

use App\Models\Area;
use App\Models\Barang;
use App\Models\MaintenanceReq;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

class ChiefStats extends StatsOverviewWidget
{
    public static ?int $sort = 0;

    public static function canView(): bool
    {
        return Auth::user()?->role === 'chief_engineering';
    }

    protected function getCards(): array
    {
        return [
            Card::make('Total Maintenance', MaintenanceReq::where('status', '!=', 'done')->count()) // <-- Perubahan di sini
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('info'),

            Card::make('Total Area', Area::count())
                ->icon('heroicon-o-map')
                ->color('success'),

            Card::make('Total Barang', Barang::count())
                ->icon('heroicon-o-cube')
                ->color('warning'),
        ];
    }
}
