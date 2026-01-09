<?php

namespace Modules\Hostels\Filament\Resources\HostelChargeResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\HostelChargeResource;

    class CreateHostelCharge extends CreateRecord {
        protected static string $resource = HostelChargeResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
