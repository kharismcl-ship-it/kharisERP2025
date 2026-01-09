<?php

namespace App\Filament\Resources\HostelFloorResource\Pages;

    use App\Filament\Resources\HostelFloorResource;
    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;

    class ListHostelFloors extends ListRecords {
        protected static string $resource = HostelFloorResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
