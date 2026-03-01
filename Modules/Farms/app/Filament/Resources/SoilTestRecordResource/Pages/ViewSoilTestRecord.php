<?php
namespace Modules\Farms\Filament\Resources\SoilTestRecordResource\Pages;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\SoilTestRecordResource;
class ViewSoilTestRecord extends ViewRecord {
    protected static string $resource = SoilTestRecordResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
}
