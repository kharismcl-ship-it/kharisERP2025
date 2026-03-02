<?php

namespace Modules\HR\Filament\Resources\PerformanceReviewResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\PerformanceReviewResource;
use Filament\Actions\CreateAction;

class ListPerformanceReviews extends ListRecords
{
    protected static string $resource = PerformanceReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
