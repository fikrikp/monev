<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BarangChartRoom extends ChartWidget
{
    protected static ?string $heading = 'Kondisi Barang Inventaris Area Room';
    protected static string $chart = 'bar';
    public static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $areaName = "Room";
        $itemNames = Barang::whereHas('room.area', fn(Builder $query) => $query->where('area_name', $areaName))
            ->select('item_name')
            ->distinct()
            ->pluck('item_name');

        $baikData = [];
        $rusakData = [];
        $lineData = [];

        foreach ($itemNames as $itemName) {
            $totalCount = Barang::where('item_name', $itemName)
                ->whereHas('room.area', fn(Builder $query) => $query->where('area_name', $areaName))
                ->count();
            $baikCount = Barang::where('item_name', $itemName)
                ->where('condition', 'baik')
                ->whereHas('room.area', fn(Builder $query) => $query->where('area_name', $areaName))
                ->count();
            $rusakCount = Barang::where('item_name', $itemName)
                ->where('condition', 'rusak')
                ->whereHas('room.area', fn(Builder $query) => $query->where('area_name', $areaName))
                ->count();

            $baikData[] = $baikCount;
            $rusakData[] = $rusakCount;
            $lineData[] = $totalCount * 0.20;
        }

        return [
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Baik',
                    'data' => $baikData,
                    'backgroundColor' => '#4ade80',
                ],
                [
                    'type' => 'bar',
                    'label' => 'Rusak',
                    'data' => $rusakData,
                    'backgroundColor' => '#f87171',
                ],
                [
                    'type' => 'line',
                    'label' => '20% dari Total Barang',
                    'data' => $lineData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderWidth' => 2,
                    'fill' => false,
                    'tension' => 0,
                ],
            ],
            'labels' => $itemNames->toArray(),
        ];
    }

    protected function getOptions(): ?array
    {
        $areaName = "Room";
        $totalRoomItems = Barang::whereHas('room.area', fn(Builder $query) => $query->where('area_name', $areaName))->count();

        return [
            'responsive' => true,
            'plugins' => [
                'legend' => ['labels' => ['color' => 'rgba(0,0,0,0.7)']],
                'title' => [
                    'display' => true,
                    'text' => 'Total Barang di Room: ' . $totalRoomItems,
                ]
            ],
            'scales' => [
                'x' => [
                    'title' => ['display' => true, 'text' => 'Nama Barang', 'color' => '#000'],
                    'ticks' => ['color' => '#000'],
                ],
                'y' => [
                    'title' => ['display' => true, 'text' => 'Jumlah Barang', 'color' => '#000'],
                    'ticks' => ['beginAtZero' => true, 'color' => '#000'],
                    'min' => 0,
                ],
            ],
            'aspectRatio' => 2,
        ];
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->role === 'chief_engineering';
    }
}
