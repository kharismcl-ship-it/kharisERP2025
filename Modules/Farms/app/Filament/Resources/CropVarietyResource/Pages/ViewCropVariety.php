<?php
namespace Modules\Farms\Filament\Resources\CropVarietyResource\Pages;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\Farms\Filament\Resources\CropVarietyResource;
class ViewCropVariety extends ViewRecord {
    protected static string $resource = CropVarietyResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
}
