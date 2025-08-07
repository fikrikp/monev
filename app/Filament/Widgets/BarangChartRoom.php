<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BarangChartRoom extends ChartWidget
{
    protected static ?string $heading = 'Monitoring Barang Inventaris Area Room';
    protected static string $chart = 'bar';
    public static ?int $sort = 2;

    // Properti baru untuk menyimpan status apakah diperlukan daily worker
    protected bool $needsDailyWorker = false;

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

            // Memeriksa jika jumlah barang rusak melebihi 20% dari total
            if ($rusakCount > ($totalCount * 0.20)) {
                $this->needsDailyWorker = true;
            }
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

        // Mengatur teks judul secara dinamis berdasarkan kondisi
        $titleText = 'Evaluasi : ';
        if ($this->needsDailyWorker) {
            $titleText .= 'Diperlukan Daily Worker';
        } else {
            $titleText .= 'Tidak ada'; // Tambahkan teks ini jika kondisi tidak terpenuhi
        }

        return [
            'responsive' => true,
            'plugins' => [
                'legend' => ['labels' => ['color' => 'rgba(0,0,0,0.7)']],
                'title' => [
                    'display' => true,
                    'text' => $titleText, // Menggunakan teks judul yang sudah dimodifikasi
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
            'aspectRatio' => 1.5,
        ];
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->role === 'chief_engineering';
    }

    public static function getWidgetWidth(): string
    {
        return 'full';
    }
}
