<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceReq;
use App\Models\Room;
use App\Models\Area;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\ChartWidget;

class MaintenanceStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Monitoring Perbaikan Barang Inventaris';
    protected static string $chart = 'bar';
    public static ?int $sort = 1;

    protected function getData(): array
    {
        $areas = Area::whereIn('area_name', ['Room', 'Meeting Room'])->get();
        $statuses = ['our_purchasing', 'waiting_material', 'in_progress', 'done'];

        $labels = [];
        $datasets = [];

        // Get area names as labels
        foreach ($areas as $area) {
            $labels[] = $area->area_name;
        }

        // Initialize datasets for each status
        $statusColors = [
            'our_purchasing' => '#f87171',
            'waiting_material' => '#fbbf24',
            'in_progress' => '#3b82f6',
            'done' => '#22c55e',
        ];

        $statusLabels = [
            'our_purchasing' => 'Our Purchasing',
            'waiting_material' => 'Waiting Material',
            'in_progress' => 'In Progress',
            'done' => 'Done',
        ];

        foreach ($statuses as $status) {
            $data = [];

            foreach ($areas as $area) {
                $count = MaintenanceReq::whereHas('barang.room', function (Builder $query) use ($area) {
                    $query->where('id_area', $area->id);
                })
                    ->where('status', $status)
                    ->count();

                $data[] = $count;
            }

            $datasets[] = [
                'label' => $statusLabels[$status],
                'data' => $data,
                'backgroundColor' => $statusColors[$status],
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): ?array
    {
        // Calculate totals for evaluation
        $ourPurchasingCount = MaintenanceReq::where('status', 'our_purchasing')->count();
        $waitingMaterialCount = MaintenanceReq::where('status', 'waiting_material')->count();
        $inProgressCount = MaintenanceReq::where('status', 'in_progress')->count();

        $needsAlternativeVendor = ($ourPurchasingCount + $waitingMaterialCount) > $inProgressCount;

        $titleText = 'Evaluasi: ';
        $titleText .= $needsAlternativeVendor
            ? 'Pertimbangkan alternatif vendor'
            : 'Tidak ada';

        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => '#374151',
                    ]
                ],
                'title' => [
                    'display' => true,
                    'text' => $titleText,
                ]
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'title' => ['display' => true, 'text' => 'Ruangan', 'color' => '#000'],
                    'ticks' => ['color' => '#000', 'maxRotation' => 45],
                ],
                'y' => [
                    'title' => ['display' => true, 'text' => 'Jumlah Permintaan', 'color' => '#000'],
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1, 'color' => '#000'],
                ],
            ],
            'aspectRatio' => 2,
        ];
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->role === 'supervisor';
    }

    public static function getWidgetWidth(): string
    {
        return 'full';
    }
}
