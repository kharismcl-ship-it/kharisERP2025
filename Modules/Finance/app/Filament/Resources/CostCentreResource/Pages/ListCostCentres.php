<?php
namespace Modules\Finance\Filament\Resources\CostCentreResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\CostCentreResource;
class ListCostCentres extends ListRecords {
    protected static string $resource = CostCentreResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
