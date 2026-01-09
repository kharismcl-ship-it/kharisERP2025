<?php

namespace App\Filament\Resources\HostelFloorResource\Pages;

    use App\Filament\Resources\HostelFloorResource;
    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;

    class EditHostelFloor extends EditRecord {
        protected static string $resource = HostelFloorResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
