<?php

namespace Modules\Hostels\Filament\Resources\TenantResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\TenantResource;

    class CreateTenant extends CreateRecord {
        protected static string $resource = TenantResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
