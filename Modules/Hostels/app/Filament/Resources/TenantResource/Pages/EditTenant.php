<?php

namespace Modules\Hostels\Filament\Resources\TenantResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\Hostels\Filament\Resources\TenantResource;

    class EditTenant extends EditRecord {
        protected static string $resource = TenantResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
