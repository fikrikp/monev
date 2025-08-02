<?php

namespace App\Filament\Widgets;

use App\Models\Area;
use App\Models\Barang;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\HtmlString;

class AdminStats extends StatsOverviewWidget
{

    public static function canView(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->role === 'admin';
    }

    protected function getCards(): array
    {
        return [
            Card::make('Total User', User::count())
                ->icon('heroicon-o-user-group')
                ->color('info'),

            Card::make('Total Area', Area::count())
                ->icon('heroicon-o-home-modern')
                ->color('success'),

            Card::make('Total Barang', Barang::count())
                ->icon('heroicon-o-cube')
                ->color('warning'),
        ];
    }
}
