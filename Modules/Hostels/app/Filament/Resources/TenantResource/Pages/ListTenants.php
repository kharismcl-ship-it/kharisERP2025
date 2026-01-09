<?php

namespace Modules\Hostels\Filament\Resources\TenantResource\Pages;

    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;
    use Modules\Hostels\Filament\Resources\TenantResource;

    class ListTenants extends ListRecords {
        protected static string $resource = TenantResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
