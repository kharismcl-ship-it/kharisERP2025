<?php

namespace Modules\Hostels\Filament\Resources\HostelMoviePurchaseResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Hostels\Filament\Resources\HostelMoviePurchaseResource;

class ListHostelMoviePurchases extends ListRecords
{
    protected static string $resource = HostelMoviePurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
