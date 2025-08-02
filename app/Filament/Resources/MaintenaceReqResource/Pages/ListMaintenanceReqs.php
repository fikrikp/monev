<?php

namespace App\Filament\Resources\MaintenanceReqResource\Pages;

use App\Filament\Resources\MaintenanceReqResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceReqs extends ListRecords
{
    protected static string $resource = MaintenanceReqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
        ];
    }
}
