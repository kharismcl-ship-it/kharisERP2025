<?php
namespace Modules\Farms\Filament\Resources\LivestockEventResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Farms\Filament\Resources\LivestockEventResource;
class ListLivestockEvents extends ListRecords {
    protected static string $resource = LivestockEventResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
