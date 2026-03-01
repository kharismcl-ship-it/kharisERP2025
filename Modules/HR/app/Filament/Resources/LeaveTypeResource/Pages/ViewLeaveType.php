<?php

namespace Modules\HR\Filament\Resources\LeaveTypeResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\LeaveTypeResource;

class ViewLeaveType extends ViewRecord
{
    protected static string $resource = LeaveTypeResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->description('Employee Details')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('name')
                            ->label('Leave Type Name'),
                        TextEntry::make('code')
                            ->label('Code'),
                        TextEntry::make('company.name')
                            ->label('Company'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Configuration')
                    ->description('Leave Type Configuration')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('max_days_per_year')
                            ->label('Maximum Days Per Year'),
                        IconEntry::make('requires_approval')
                            ->label('Requires Approval')
                            ->boolean(),
                        IconEntry::make('is_paid')
                            ->label('Is Paid Leave')
                            ->boolean(),
                        IconEntry::make('is_active')
                            ->label('Is Active')
                            ->boolean(),
                    ])->columns(2),

                Section::make('Audit Information')
                    ->description('Audit Information')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('view_leave_requests')
                ->label('View Leave Requests')
                ->url(fn ($record) => LeaveTypeResource::getUrl('index', ['tableFilters' => ['leave_type_id' => ['value' => $record->id]]])),
        ];
    }
}
