<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
// ✅ Import Resource yang dibutuhkan untuk mendapatkan URL-nya
use App\Filament\Resources\UserResource;
use App\Filament\Resources\RoomResource; // Pastikan ini AreaResource jika untuk 'Add Area'
use App\Filament\Resources\BarangResource;
use App\Filament\Resources\ReportResource;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    // ✅ Menggunakan custom Blade view untuk widget ini
    protected static string $view = 'filament.widgets.quick-actions-widget';

    // ✅ Atur lebar kolom widget di dashboard (misalnya, penuh)
    protected int | string | array $columnSpan = 'full';

    // ✅ Widget ini hanya ditampilkan untuk admin
    public static function canView(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    // ✅ Mengirim data (URL) ke Blade view
    protected function getViewData(): array
    {
        return [
            'addUserUrl' => UserResource::getUrl('create'),
            'addAreaUrl' => RoomResource::getUrl('index'), // Sesuaikan jika ini RoomResource::getUrl('create')
            'addBarangUrl' => BarangResource::getUrl('create'),
            'viewReportUrl' => ReportResource::getUrl('index'),
        ];
    }
}
