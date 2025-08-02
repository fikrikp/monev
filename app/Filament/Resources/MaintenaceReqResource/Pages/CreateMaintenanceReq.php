<?php

namespace App\Filament\Resources\MaintenanceReqResource\Pages;

use App\Filament\Resources\MaintenanceReqResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMaintenanceReq extends CreateRecord
{
    protected static string $resource = MaintenanceReqResource::class;

    protected function afterCreate(): void
    {
        // Update status barang yang dipilih jadi 'rusak'
        $this->record->barang->update([
            'condition' => 'rusak',
        ]);
    }


    protected function getRedirectUrl(): string
    {
        return MaintenanceReqResource::getUrl('index');
    }
}
