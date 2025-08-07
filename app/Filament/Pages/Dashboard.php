<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

use App\Filament\Widgets\AdminStats;
use App\Filament\Widgets\SupervisorStats;
use App\Filament\Widgets\ChiefStats;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\BarangChartMeetingRoom;
use App\Filament\Widgets\BarangChartRoom;
use App\Filament\Widgets\MaintenanceStatusChart;
use Illuminate\Support\Facades\Auth;


class Dashboard extends BaseDashboard
{

    public static function canAccess(): bool
    {
        return Auth::user()?->role !== 'staff';
    }
}
