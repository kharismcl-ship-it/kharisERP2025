<?php

namespace App\Filament\Resources\HostelBlockResource\Pages;

    use App\Filament\Resources\HostelBlockResource;
    use Filament\Actions\CreateAction;
    use Filament\Resources\Pages\ListRecords;

    class ListHostelBlocks extends ListRecords {
        protected static string $resource = HostelBlockResource::class;

        protected function getHeaderActions(): array {
        return [
        CreateAction::make(),
        ];
        }
    }
