<?php

namespace Modules\HR\Filament\Resources\LeaveBalanceResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\LeaveBalanceResource;
use Modules\HR\Models\LeaveBalance;

class ViewLeaveBalance extends ViewRecord
{
    protected static string $resource = LeaveBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Balance Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('employee.full_name')
                            ->label('Employee')
                            ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                            ->weight('bold'),
                        TextEntry::make('leaveType.name')->label('Leave Type'),
                        TextEntry::make('year')->label('Year'),
                        TextEntry::make('initial_balance')->label('Entitlement')->suffix(' days'),
                        TextEntry::make('used_balance')->label('Used')->suffix(' days'),
                        TextEntry::make('current_balance')
                            ->label('Remaining Balance')
                            ->suffix(' days')
                            ->color(fn (LeaveBalance $record): string => $record->current_balance <= 2 ? 'danger' : 'success'),
                        TextEntry::make('carried_over')->label('Carried Over')->suffix(' days')->placeholder('0'),
                        TextEntry::make('adjustments')->label('Adjustments')->suffix(' days')->placeholder('0'),
                        TextEntry::make('last_calculated_at')->label('Last Calculated')->dateTime()->placeholder('—'),
                    ]),

                Section::make('Notes')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('notes')->columnSpanFull()->placeholder('No notes.'),
                    ]),
            ]);
    }
}
