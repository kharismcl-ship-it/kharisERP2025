<?php

namespace Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MyLeaveRequestResource;

class ListMyLeaveRequests extends ListRecords
{
    protected static string $resource = MyLeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Request Leave'),
        ];
    }
}
