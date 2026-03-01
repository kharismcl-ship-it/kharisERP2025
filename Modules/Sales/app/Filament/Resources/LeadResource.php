<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\LeadResource\Pages;
use Modules\Sales\Models\SalesLead;

class LeadResource extends Resource
{
    protected static ?string $model = SalesLead::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-user-plus';
    protected static string|\UnitEnum|null   $navigationGroup = 'CRM';
    protected static ?int                    $navigationSort  = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lead Details')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->maxLength(255),
                TextInput::make('phone')->tel()->maxLength(50),
                TextInput::make('company_name')->maxLength(255),
                Select::make('source')
                    ->options(array_combine(SalesLead::SOURCES, array_map('ucfirst', SalesLead::SOURCES)))
                    ->default('web'),
                Select::make('status')
                    ->options(array_combine(SalesLead::STATUSES, array_map('ucfirst', SalesLead::STATUSES)))
                    ->default('new')
                    ->required(),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('company_name')->label('Company'),
                TextColumn::make('source')->badge(),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'new'       => 'info',
                        'contacted' => 'warning',
                        'qualified' => 'success',
                        'lost'      => 'danger',
                        'converted' => 'success',
                        default     => 'gray',
                    }),
                TextColumn::make('assignedTo.name')->label('Assigned To'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options(array_combine(SalesLead::STATUSES, array_map('ucfirst', SalesLead::STATUSES))),
                SelectFilter::make('source')->options(array_combine(SalesLead::SOURCES, array_map('ucfirst', SalesLead::SOURCES))),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit'   => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
