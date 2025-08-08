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
        $itemNames = Barang::whereHas('room.area', fn(Builder $query) =>
        $query->where('area_name', $areaName))
            ->select('item_name')
            ->distinct()
            ->pluck('item_name');

        $baikData = [];
        $rusakData = [];

        // --- Hitung total semua barang & jumlah jenis barang ---
        $totalSemuaBarang = 0;
        foreach ($itemNames as $itemName) {
            $totalCount = Barang::where('item_name', $itemName)
                ->whereHas('room.area', fn(Builder $query) =>
                $query->where('area_name', $areaName))
                ->count();
            $totalSemuaBarang += $totalCount;
        }

        $jumlahJenisBarang = count($itemNames);
        $rataRata = $totalSemuaBarang / $jumlahJenisBarang;
        $batasKerusakan = ceil($rataRata * 0.20); // 20% dari rata-rata, dibulatkan ke atas

        // --- Isi data baik, rusak, dan threshold ---
        $lineData = [];
        foreach ($itemNames as $itemName) {
            $baikCount = Barang::where('item_name', $itemName)
                ->where('condition', 'baik')
                ->whereHas('room.area', fn(Builder $query) =>
                $query->where('area_name', $areaName))
                ->count();

            $rusakCount = Barang::where('item_name', $itemName)
                ->where('condition', 'rusak')
                ->whereHas('room.area', fn(Builder $query) =>
                $query->where('area_name', $areaName))
                ->count();

            $baikData[] = $baikCount;
            $rusakData[] = $rusakCount;
            $lineData[] = $batasKerusakan;

            // Cek apakah rusak melebihi batas
            if ($rusakCount > $batasKerusakan) {
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
                    'label' => 'Batas Kerusakan',
                    'data' => $lineData,
                    'borderColor' => '#fbbf24',
                    'backgroundColor' => '#fbbe24a9',
                    'borderWidth' => 3,
                    'fill' => false,
                    'tension' => 0.4,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 0,
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
            $titleText .= 'Tidak ada';
        }

        return [
            'responsive' => true,
            'plugins' => [
                'legend' => ['labels' => ['color' => 'rgba(0,0,0,0.7)']],
                'title' => [
                    'display' => true,
                    'text' => $titleText,
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
