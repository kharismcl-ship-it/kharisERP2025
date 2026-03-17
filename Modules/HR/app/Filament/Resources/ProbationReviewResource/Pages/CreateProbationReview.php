<?php

namespace Modules\HR\Filament\Resources\ProbationReviewResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\HR\Filament\Resources\ProbationReviewResource;

class CreateProbationReview extends CreateRecord
{
    protected static string $resource = ProbationReviewResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
