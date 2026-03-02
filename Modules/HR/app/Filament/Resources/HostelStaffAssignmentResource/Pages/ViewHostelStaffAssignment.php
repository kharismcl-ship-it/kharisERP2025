<?php

namespace Modules\HR\Filament\Resources\HostelStaffAssignmentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\HostelStaffAssignmentResource;

class ViewHostelStaffAssignment extends ViewRecord
{
    protected static string $resource = HostelStaffAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Assignment Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('company.name')->label('Company'),
                    TextEntry::make('employee.full_name')->label('Employee')->weight('bold'),
                    TextEntry::make('hostel.name')->label('Hostel'),
                    TextEntry::make('role')->placeholder('—'),
                ]),

            Section::make('Dates')
                ->columns(2)
                ->schema([
                    TextEntry::make('assigned_at')->dateTime()->label('Assigned At')->placeholder('—'),
                    TextEntry::make('expires_at')->dateTime()->label('Expires At')->placeholder('—'),
                ]),

            Section::make('Audit')
                ->columns(2)
                ->collapsible()->collapsed()
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('Created'),
                    TextEntry::make('updated_at')->dateTime()->label('Last Updated'),
                ]),
        ]);
    }
}