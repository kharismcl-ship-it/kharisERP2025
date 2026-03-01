<?php
namespace Modules\Farms\Filament\Resources\SoilTestRecordResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\SoilTestRecordResource;
class EditSoilTestRecord extends EditRecord {
    protected static string $resource = SoilTestRecordResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
