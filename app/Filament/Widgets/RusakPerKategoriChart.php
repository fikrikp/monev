<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RusakPerKategoriChart extends ChartWidget
{
    protected static ?string $heading = 'Barang Rusak per Kategori';

    // Properti baru untuk menyimpan kategori dengan kerusakan terbanyak
    protected ?string $mostDamagedCategory = null;
    protected int $maxDamagedCount = 0;

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

            // Logika untuk menemukan kategori dengan kerusakan terbanyak
            if ($count > $this->maxDamagedCount) {
                $this->maxDamagedCount = $count;
                $this->mostDamagedCategory = $kategori->category_name;
            }
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
        $titleText = 'Evaluasi : ';

        // Logika untuk menentukan teks evaluasi
        if ($this->mostDamagedCategory && $this->maxDamagedCount > 0) {
            $titleText .= 'Membutuhkan daily worker ' . $this->mostDamagedCategory;
        } else {
            $titleText .= 'Tidak ada';
        }

        return [
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => '#374151',
                    ]
                ],
                'title' => [
                    'display' => true,
                    'text' => $titleText, // Menggunakan teks judul yang sudah dimodifikasi
                ]
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

    public static function getWidgetWidth(): string
    {
        return 'full';
    }

    public static ?int $sort = 3;
}
