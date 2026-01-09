<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HostelOccupantsRelationManager extends RelationManager
{
    protected static string $relationship = 'hostelOccupants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('other_names')
                    ->maxLength(255),
                TextInput::make('full_name')
                    ->maxLength(255),
                Select::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
                    ])
                    ->required(),
                DatePicker::make('dob')
                    ->label('Date of Birth'),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('alt_phone')
                    ->tel(),
                TextInput::make('email')
                    ->email(),
                TextInput::make('national_id_number')
                    ->label('National ID Number'),
                TextInput::make('student_id'),
                TextInput::make('institution'),
                TextInput::make('guardian_name'),
                TextInput::make('guardian_phone')
                    ->tel(),
                TextInput::make('guardian_email')
                    ->email(),
                TextInput::make('address'),
                TextInput::make('emergency_contact_name'),
                TextInput::make('emergency_contact_phone')
                    ->tel(),
                Select::make('status')
                    ->options([
                        'prospect' => 'Prospect',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blacklisted' => 'Blacklisted',
                    ])
                    ->required()
                    ->default('prospect'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name'),
                TextColumn::make('gender'),
                TextColumn::make('phone'),
                TextColumn::make('email'),
                TextColumn::make('student_id'),
                TextColumn::make('institution'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prospect' => 'info',
                        'active' => 'success',
                        'inactive' => 'warning',
                        'blacklisted' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('bookings_count')
                    ->label('Bookings')
                    ->counts('bookings'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
