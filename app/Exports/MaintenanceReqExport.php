<?php

namespace App\Exports;

use App\Models\MaintenanceReq;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MaintenanceReqExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = MaintenanceReq::with(['barang.room', 'barang.category'])
            ->where('status', 'done');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->get()->map(function ($item) {
            return [
                'Tanggal Upload'    => $item->created_at->format('Y-m-d'),
                'Barang Inventaris' => $item->barang->item_name ?? '-',
                'Lokasi'            => $item->barang->room->room_name ?? '-',
                'Problem Request'   => $item->problem ?? '-',
                'Evaluasi'          => $item->evaluasi ?? '-',
                'Kategori'          => $item->barang->category->category_name ?? '-',
                'Status'            => $item->status ?? '-',
                'Tanggal Selesai'   => $item->updated_at?->format('Y-m-d') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal Upload',
            'Barang Inventaris',
            'Lokasi',
            'Problem Request',
            'Evaluasi',
            'Kategori',
            'Status',
            'Tanggal Selesai',
        ];
    }
}
