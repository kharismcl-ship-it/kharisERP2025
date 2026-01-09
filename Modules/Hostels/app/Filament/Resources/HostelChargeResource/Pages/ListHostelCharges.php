<?php

namespace Modules\Hostels\Filament\Resources\HostelChargeResource\Pages;

    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;
    use Modules\Hostels\Filament\Resources\HostelChargeResource;

    class ListHostelCharges extends ListRecords {
        protected static string $resource = HostelChargeResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
