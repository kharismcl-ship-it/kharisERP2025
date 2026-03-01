<?php

namespace Modules\HR\Filament\Resources\EmployeeDocumentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\EmployeeDocumentResource;

class ViewEmployeeDocument extends ViewRecord
{
    protected static string $resource = EmployeeDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Details')->columns(2)->schema([
                TextEntry::make('employee_name')->label('Employee')
                    ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->weight('bold'),
                TextEntry::make('document_type')->label('Type')
                    ->formatStateUsing(fn ($state) => strtoupper($state ?? '')),
                TextEntry::make('company.name')->label('Company'),
                TextEntry::make('uploadedBy.name')->label('Uploaded By')->placeholder('—'),
                TextEntry::make('description')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}