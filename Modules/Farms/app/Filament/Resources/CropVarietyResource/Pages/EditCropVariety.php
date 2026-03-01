<?php
namespace Modules\Farms\Filament\Resources\CropVarietyResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\CropVarietyResource;
class EditCropVariety extends EditRecord {
    protected static string $resource = CropVarietyResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
