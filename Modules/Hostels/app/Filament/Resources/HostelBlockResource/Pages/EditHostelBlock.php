<?php

namespace App\Filament\Resources\HostelBlockResource\Pages;

    use App\Filament\Resources\HostelBlockResource;
    use Filament\Actions\DeleteAction;
    use Filament\Resources\Pages\EditRecord;

    class EditHostelBlock extends EditRecord {
        protected static string $resource = HostelBlockResource::class;

        protected function getHeaderActions(): array {
        return [
        DeleteAction::make(),
        ];
        }
    }
