<?php

namespace App\Filament\Resources\HostelFloorResource\Pages;

    use App\Filament\Resources\HostelFloorResource;
    use Filament\Resources\Pages\CreateRecord;

    class CreateHostelFloor extends CreateRecord {
        protected static string $resource = HostelFloorResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
