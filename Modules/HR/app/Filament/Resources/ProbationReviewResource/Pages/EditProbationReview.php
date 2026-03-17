<?php

namespace Modules\HR\Filament\Resources\ProbationReviewResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\ProbationReviewResource;

class EditProbationReview extends EditRecord
{
    protected static string $resource = ProbationReviewResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
