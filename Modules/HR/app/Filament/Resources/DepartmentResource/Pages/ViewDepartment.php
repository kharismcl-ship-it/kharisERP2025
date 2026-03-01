<?php

namespace Modules\HR\Filament\Resources\DepartmentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\DepartmentResource;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Department Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->weight('bold'),
                        TextEntry::make('code')->placeholder('—'),
                        TextEntry::make('company.name')->label('Company'),
                        TextEntry::make('parent.name')->label('Parent Department')->placeholder('Top-level'),
                        IconEntry::make('is_active')->label('Active')->boolean(),
                        TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                    ]),
            ]);
    }
}
