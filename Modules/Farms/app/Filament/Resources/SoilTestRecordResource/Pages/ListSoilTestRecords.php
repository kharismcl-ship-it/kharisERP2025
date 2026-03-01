<?php
namespace Modules\Farms\Filament\Resources\SoilTestRecordResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\SoilTestRecordResource;
class ListSoilTestRecords extends ListRecords {
    protected static string $resource = SoilTestRecordResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
