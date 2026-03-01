<?php
namespace Modules\Farms\Filament\Resources\CropVarietyResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\CropVarietyResource;
class ListCropVarieties extends ListRecords {
    protected static string $resource = CropVarietyResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
