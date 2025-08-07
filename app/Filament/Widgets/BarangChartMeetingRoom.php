<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BarangChartMeetingRoom extends ChartWidget
{
    // Judul widget yang akan ditampilkan di dashboard
    protected static ?string $heading = 'Kondisi Barang Inventaris Area Meeting Room';

    // Mendefinisikan tipe chart, meskipun ini mixed-chart, kita bisa set tipe dasarnya
    protected static string $chart = 'bar';

    // Menentukan urutan widget di dashboard
    public static ?int $sort = 2;

    /**
     * Get the type of chart to display
     */
    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Metode ini mengambil data dari database dan mengonfigurasinya untuk chart.
     * Ini akan dijalankan oleh Filament.
     */
    protected function getData(): array
    {
        $areaName = "Meeting Room";

        // Mengambil semua nama barang unik di area "Meeting Room"

        $itemNames = Barang::whereHas('room.area', function ($query) use ($areaName) {
            $query->where('area_name', $areaName);
        })
            ->select('item_name')
            ->distinct()
            ->pluck('item_name');

        $baikData = [];
        $rusakData = [];
        $lineData = []; // Data untuk grafik garis

        // Loop melalui setiap nama barang untuk menghitung jumlahnya
        foreach ($itemNames as $itemName) {
            // Hitung total jumlah barang per item_name
            $totalCount = Barang::where('item_name', $itemName)
                ->whereHas('room.area', function (Builder $query) use ($areaName) {
                    $query->where('area_name', $areaName);
                })
                ->count();

            // Hitung jumlah barang dengan kondisi 'baik'
            $baikCount = Barang::where('item_name', $itemName)
                ->where('condition', 'baik')
                ->whereHas('room.area', function (Builder $query) use ($areaName) {
                    $query->where('area_name', $areaName);
                })
                ->count();

            // Hitung jumlah barang dengan kondisi 'rusak'
            $rusakCount = Barang::where('item_name', $itemName)
                ->where('condition', 'rusak')
                ->whereHas('room.area', function (Builder $query) use ($areaName) {
                    $query->where('area_name', $areaName);
                })
                ->count();

            // Masukkan data ke array
            $baikData[] = $baikCount;
            $rusakData[] = $rusakCount;
            $lineData[] = $totalCount * 0.20; // Menghitung 20% dari total barang
        }

        // Mengembalikan data dalam format yang dimengerti Chart.js
        return [
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Baik',
                    'data' => $baikData,
                    'backgroundColor' => '#4ade80', // hijau
                ],
                [
                    'type' => 'bar',
                    'label' => 'Rusak',
                    'data' => $rusakData,
                    'backgroundColor' => '#f87171', // merah
                ],
                [
                    'type' => 'line',
                    'label' => '20% dari Total Barang',
                    'data' => $lineData,
                    'borderColor' => '#3b82f6', // biru
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // background biru transparan
                    'borderWidth' => 2,
                    'fill' => false,
                    'tension' => 0,
                ],
            ],
            'labels' => $itemNames->toArray(),
        ];
    }

    /**
     * Metode ini mengonfigurasi opsi-opsi Chart.js
     */
    protected function getOptions(): ?array
    {
        $areaName = "Meeting Room";

        // Menghitung total barang di Meeting Room untuk ditampilkan di judul
        $totalMeetingRoomItems = Barang::whereHas('room.area', function (Builder $query) use ($areaName) {
            $query->where('area_name', $areaName);
        })->count();

        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => 'rgba(0,0,0,0.7)',
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Total Barang di Meeting Room: ' . $totalMeetingRoomItems,
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

    /**
     * Metode ini membatasi siapa yang bisa melihat widget ini berdasarkan role
     */
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->role === 'chief_engineering';
    }
}
