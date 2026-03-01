<?php

namespace Modules\HR\Filament\Resources\AttendanceRecordResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\AttendanceRecordResource;

class ViewAttendanceRecord extends ViewRecord
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Attendance Record')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('employee.full_name')->label('Employee')->weight('bold'),
                        TextEntry::make('date')->date(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'present' => 'success',
                                'absent'  => 'danger',
                                'leave'   => 'warning',
                                'off'     => 'gray',
                                default   => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                        TextEntry::make('company.name')->label('Company'),
                    ]),

                Section::make('Timing')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('check_in_time')->label('Check In')->dateTime()->placeholder('—'),
                        TextEntry::make('check_out_time')->label('Check Out')->dateTime()->placeholder('—'),
                        TextEntry::make('notes')->label('Notes')->columnSpanFull()->placeholder('—'),
                    ]),
            ]);
    }
}