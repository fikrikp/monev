<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RusakPerKategoriChart extends ChartWidget
{
    protected static ?string $heading = 'Barang Rusak per Kategori';

    protected function getData(): array
    {
        // Ambil semua kategori
        $categories = Category::all();

        $labels = [];
        $data = [];

        foreach ($categories as $kategori) {
            $count = Barang::where('condition', 'rusak')
                ->where('category_id', $kategori->id)
                ->count();

            $labels[] = $kategori->category_name;
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Rusak',
                    'data' => $data,
                    'backgroundColor' => '#f87171', // merah
                ],
            ],
            'labels' => $labels,
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
                    'ticks' => [
                        'autoSkip' => false,
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
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

    public static ?int $sort = 3;
}
