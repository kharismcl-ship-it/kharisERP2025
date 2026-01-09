<?php

namespace Modules\Hostels\Filament\Resources\VisitorLogResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\VisitorLogResource;

    class CreateVisitorLog extends CreateRecord {
        protected static string $resource = VisitorLogResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
