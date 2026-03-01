<?php

namespace Modules\HR\Filament\Resources\LeaveRequestResource\Pages;

use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Actions\ApproveLeaveAction;
use Modules\HR\Filament\Actions\RejectLeaveAction;
use Modules\HR\Filament\Resources\LeaveRequestResource;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Leave Request Details')
                    ->description('Leave Request Details')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('employee.full_name')
                            ->label('Employee'),
                        TextEntry::make('leaveType.name')
                            ->label('Leave Type'),
                        TextEntry::make('company.name')
                            ->label('Company'),
                        TextEntry::make('duration_in_days')
                            ->label('Duration (Days)')
                            ->getStateUsing(fn ($record) => $record->start_date->diffInDays($record->end_date) + 1),
                        IconEntry::make('leaveType.has_accrual')
                            ->label('Accrual Enabled')
                            ->boolean(),
                        TextEntry::make('leaveType.accrual_rate')
                            ->label('Accrual Rate')
                            ->suffix(' days/month')
                            ->visible(fn ($record) => $record->leaveType->has_accrual),
                        TextEntry::make('leaveType.accrual_frequency')
                            ->label('Accrual Frequency')
                            ->visible(fn ($record) => $record->leaveType->has_accrual),
                    ])->columns(2),

                Section::make('Date Information')
                    ->description('Date Information')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->label('End Date')
                            ->date(),
                        TextEntry::make('total_days')
                            ->label('Total Days')
                            ->numeric(),
                    ])->columns(3),

                Section::make('Leave Balance Information')
                    ->description('Current Employee Leave Balance')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('employee.current_leave_balance')
                            ->label('Current Balance')
                            ->getStateUsing(function ($record) {
                                $balance = \Modules\HR\Models\LeaveBalance::where('employee_id', $record->employee_id)
                                    ->where('leave_type_id', $record->leave_type_id)
                                    ->first();

                                return $balance ? $balance->current_balance.' days' : '0 days';
                            })
                            ->color(function ($record) {
                                $balance = \Modules\HR\Models\LeaveBalance::where('employee_id', $record->employee_id)
                                    ->where('leave_type_id', $record->leave_type_id)
                                    ->first();
                                $requestedDays = $record->start_date->diffInDays($record->end_date) + 1;

                                return ($balance && $balance->current_balance >= $requestedDays) ? 'success' : 'danger';
                            }),
                        TextEntry::make('requested_days')
                            ->label('Requested Days')
                            ->getStateUsing(function ($record) {
                                return $record->start_date->diffInDays($record->end_date) + 1 .' days';
                            }),
                        TextEntry::make('balance_after_approval')
                            ->label('Balance After Approval')
                            ->getStateUsing(function ($record) {
                                $balance = \Modules\HR\Models\LeaveBalance::where('employee_id', $record->employee_id)
                                    ->where('leave_type_id', $record->leave_type_id)
                                    ->first();
                                $requestedDays = $record->start_date->diffInDays($record->end_date) + 1;
                                $newBalance = ($balance ? $balance->current_balance : 0) - $requestedDays;

                                return $newBalance.' days';
                            })
                            ->color(function ($record) {
                                $balance = \Modules\HR\Models\LeaveBalance::where('employee_id', $record->employee_id)
                                    ->where('leave_type_id', $record->leave_type_id)
                                    ->first();
                                $requestedDays = $record->start_date->diffInDays($record->end_date) + 1;

                                return ($balance && $balance->current_balance >= $requestedDays) ? 'success' : 'danger';
                            }),
                    ])->columns(3),

                Section::make('Status & Approval')
                    ->description('Status & Approval')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        IconEntry::make('status')
                            ->label('Status')
                            ->icon(function ($get) {
                                $state = $get('status');

                                return match ($state) {
                                    'approved' => 'heroicon-o-check-circle',
                                    'rejected' => 'heroicon-o-x-circle',
                                    'pending' => 'heroicon-o-clock',
                                    'cancelled' => 'heroicon-o-ban',
                                    default => 'heroicon-o-question-mark-circle',
                                };
                            })
                            ->color(function ($get) {
                                $state = $get('status');

                                return match ($state) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'pending' => 'warning',
                                    'cancelled' => 'gray',
                                    default => 'gray',
                                };
                            }),
                        TextEntry::make('approved_by_employee.full_name')
                            ->label('Approved By')
                            ->placeholder('Not approved yet'),
                        TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->dateTime()
                            ->placeholder('Not approved yet'),
                    ])->columns(3),

                Section::make('Notification Preferences')
                    ->description('Employee Notification Channel Preferences')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('employee.notification_channels')
                            ->label('Preferred Channels')
                            ->getStateUsing(function ($record) {
                                $channels = $record->employee->notificationChannels;

                                return ! empty($channels) ? implode(', ', array_map('ucfirst', $channels)) : 'All channels enabled';
                            })
                            ->badge()
                            ->color('info'),
                    ]),

                Section::make('Reason & Notes')
                    ->description('Reason & Notes')
                    ->collapsible()
                    ->columns([
                        'default' => 2,
                        'sm' => 2,
                        'md' => 2,
                        'xl' => 3,
                    ])
                    ->schema([
                        TextEntry::make('reason')
                            ->label('Reason for Leave')
                            ->columnSpanFull(),
                        TextEntry::make('rejected_reason')
                            ->label('Rejection Reason')
                            ->placeholder('Not rejected')
                            ->columnSpanFull(),
                    ]),

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
            ApproveLeaveAction::make(),
            RejectLeaveAction::make(),
        ];
    }
}
