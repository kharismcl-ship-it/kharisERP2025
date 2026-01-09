<?php

namespace Modules\Hostels\Filament\Resources\BedResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\BedResource;

    class CreateBed extends CreateRecord {
        protected static string $resource = BedResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
