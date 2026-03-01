<?php

namespace Modules\HR\Filament\Resources\SalaryScaleResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\SalaryScaleResource;

class ViewSalaryScale extends ViewRecord
{
    protected static string $resource = SalaryScaleResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Salary Scale')->columns(2)->schema([
                TextEntry::make('name')->weight('bold'),
                TextEntry::make('code')->placeholder('—'),
                TextEntry::make('company.name')->label('Company'),
                TextEntry::make('currency')->default('GHS'),
                TextEntry::make('min_basic')->money('GHS')->label('Minimum Basic'),
                TextEntry::make('max_basic')->money('GHS')->label('Maximum Basic'),
                TextEntry::make('description')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}