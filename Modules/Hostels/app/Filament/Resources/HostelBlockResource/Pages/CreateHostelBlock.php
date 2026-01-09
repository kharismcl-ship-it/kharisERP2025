<?php

namespace App\Filament\Resources\HostelBlockResource\Pages;

    use App\Filament\Resources\HostelBlockResource;
    use Filament\Resources\Pages\CreateRecord;

    class CreateHostelBlock extends CreateRecord {
        protected static string $resource = HostelBlockResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
