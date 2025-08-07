<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceReq;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MaintenanceStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Permintaan Maintenance';

    protected function getData(): array
    {
        $statuses = ['our_purchasing', 'waiting_material', 'in_progress', 'done'];

        $data = [];

        foreach ($statuses as $status) {
            $data[] = MaintenanceReq::where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permintaan',
                    'data' => $data,
                    'backgroundColor' => '#60a5fa',
                ],
            ],
            'labels' => [
                'Our Purchasing',
                'Waiting Material',
                'In Progress',
                'Done',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): ?array
    {
        return [
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => '#374151',
                    ]
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                ],
            ],
            'aspectRatio' => 2,
        ];
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->role === 'supervisor';
    }

    public static ?int $sort = 1;
}
