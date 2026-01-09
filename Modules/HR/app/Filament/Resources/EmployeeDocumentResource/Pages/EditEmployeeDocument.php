<?php

namespace Modules\HR\Filament\Resources\EmployeeDocumentResource\Pages;

    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;
    use Modules\HR\Filament\Resources\EmployeeDocumentResource;

    class EditEmployeeDocument extends EditRecord {
        protected static string $resource = EmployeeDocumentResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
