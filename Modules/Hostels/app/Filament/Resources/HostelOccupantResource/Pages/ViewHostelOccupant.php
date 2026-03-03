<?php

namespace Modules\Hostels\Filament\Resources\HostelOccupantResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Hostels\Filament\Resources\HostelOccupantResource;

class ViewHostelOccupant extends ViewRecord
{
    protected static string $resource = HostelOccupantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Personal Information')
                ->schema([
                    TextEntry::make('hostel.name')->label('Hostel'),
                    TextEntry::make('full_name')->label('Full Name'),
                    TextEntry::make('first_name')->label('First Name'),
                    TextEntry::make('last_name')->label('Last Name'),
                    TextEntry::make('gender')
                        ->label('Gender')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'male'   => 'info',
                            'female' => 'warning',
                            default  => 'gray',
                        }),
                    TextEntry::make('dob')->label('Date of Birth')->date()->placeholder('—'),
                ])
                ->columns(2),

            Section::make('Contact Details')
                ->schema([
                    TextEntry::make('phone')->label('Phone'),
                    TextEntry::make('alt_phone')->label('Alternate Phone')->placeholder('—'),
                    TextEntry::make('email')->label('Email')->placeholder('—'),
                ])
                ->columns(3),

            Section::make('Academic / Identity')
                ->schema([
                    TextEntry::make('student_id')->label('Student ID')->placeholder('—'),
                    TextEntry::make('institution')->label('Institution')->placeholder('—'),
                    TextEntry::make('national_id_number')->label('National ID Number')->placeholder('—'),
                ])
                ->columns(3),

            Section::make('Guardian')
                ->schema([
                    TextEntry::make('guardian_name')->label('Guardian Name')->placeholder('—'),
                    TextEntry::make('guardian_phone')->label('Guardian Phone')->placeholder('—'),
                    TextEntry::make('guardian_email')->label('Guardian Email')->placeholder('—'),
                ])
                ->columns(3),

            Section::make('Status')
                ->schema([
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'      => 'success',
                            'prospect'    => 'warning',
                            'inactive'    => 'gray',
                            'blacklisted' => 'danger',
                            default       => 'gray',
                        }),
                    TextEntry::make('check_in_date')->label('Check-in Date')->date()->placeholder('Not set'),
                    TextEntry::make('check_out_date')->label('Check-out Date')->date()->placeholder('Not set'),
                ])
                ->columns(3),
        ]);
    }
}
