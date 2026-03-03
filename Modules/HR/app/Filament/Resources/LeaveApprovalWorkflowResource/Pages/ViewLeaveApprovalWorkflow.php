<?php

namespace Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
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

                Section::make('Approval Levels')
                    ->schema([
                        RepeatableEntry::make('levels')
                            ->schema([
                                TextEntry::make('level_number')->label('Level #'),
                                TextEntry::make('approver_type')
                                    ->label('Type')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'manager' => 'Direct Manager',
                                        'department_head' => 'Department Head',
                                        'specific_employee' => 'Specific Employee',
                                        'hr' => 'HR Representative',
                                        default => $state,
                                    }),
                                TextEntry::make('approverEmployee.full_name')
                                    ->label('Specific Employee')
                                    ->placeholder('—'),
                                TextEntry::make('approverDepartment.name')
                                    ->label('Department')
                                    ->placeholder('—'),
                                TextEntry::make('approver_role')
                                    ->label('Role Pattern')
                                    ->placeholder('—'),
                                IconEntry::make('is_required')->label('Required')->boolean(),
                                TextEntry::make('approval_order')->label('Order'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
