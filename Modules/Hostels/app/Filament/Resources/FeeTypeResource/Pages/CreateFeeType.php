<?php

namespace Modules\Hostels\Filament\Resources\FeeTypeResource\Pages;

    use Filament\Resources\Pages\CreateRecord;
    use Modules\Hostels\Filament\Resources\FeeTypeResource;

    class CreateFeeType extends CreateRecord {
        protected static string $resource = FeeTypeResource::class;

        protected function getHeaderActions(): array {
        return [

        ];
        }
    }
