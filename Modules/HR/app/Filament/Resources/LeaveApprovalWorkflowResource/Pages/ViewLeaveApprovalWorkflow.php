<?php

namespace Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource;

class ViewLeaveApprovalWorkflow extends ViewRecord
{
    protected static string $resource = LeaveApprovalWorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Workflow Configuration')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->weight('bold'),
                        TextEntry::make('company.name')->label('Company'),
                        IconEntry::make('is_active')->label('Active')->boolean(),
                        IconEntry::make('requires_all_approvals')->label('Requires All Approvals')->boolean(),
                        TextEntry::make('timeout_days')->label('Approval Timeout')->suffix(' days')->placeholder('No timeout'),
                        TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                    ]),
            ]);
    }
}