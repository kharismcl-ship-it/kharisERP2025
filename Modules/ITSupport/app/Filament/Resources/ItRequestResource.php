<?php

namespace Modules\ITSupport\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ITSupport\Filament\Resources\ItRequestResource\Pages;
use Modules\ITSupport\Models\ItRequest;

class ItRequestResource extends Resource
{
    protected static ?string $model = ItRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'IT Support';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'IT Requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')->schema([
                Grid::make(3)->schema([
                    Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ,
                    Select::make('requester_employee_id')
                        ->label('Requester')
                        ->relationship('requesterEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    Select::make('category')
                        ->options(ItRequest::CATEGORIES)
                        ->required(),
                    Select::make('priority')
                        ->options(ItRequest::PRIORITIES)
                        ->default('medium')
                        ->required(),
                ]),
                TextInput::make('subject')->required()->maxLength(255)->columnSpanFull(),
                Textarea::make('description')->required()->rows(4)->columnSpanFull(),
            ]),

            Section::make('Assignment')->schema([
                Grid::make(3)->schema([
                    Select::make('assigned_to_employee_id')
                        ->label('Assigned To')
                        ->relationship('assignedToEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    DatePicker::make('estimated_resolution_date')->label('Est. Resolution')->nullable(),
                    Select::make('status')
                        ->options(ItRequest::STATUSES)
                        ->default('open')
                        ->required(),
                ]),
            ]),

            Section::make('Resolution')->schema([
                Grid::make(2)->schema([
                    DateTimePicker::make('resolved_at')->label('Resolved At')->nullable(),
                ]),
                Textarea::make('resolution_notes')->label('Resolution Notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->badge()->searchable()->sortable(),
                TextColumn::make('subject')->searchable()->limit(45),
                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ItRequest::CATEGORIES[$state] ?? $state),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'info',
                        default    => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'open'         => 'info',
                        'in_progress'  => 'warning',
                        'pending_info' => 'gray',
                        'resolved'     => 'success',
                        'closed'       => 'success',
                        'cancelled'    => 'danger',
                        default        => 'gray',
                    }),
                TextColumn::make('requesterEmployee.full_name')->label('Requester'),
                TextColumn::make('assignedToEmployee.full_name')->label('Assigned To'),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(ItRequest::STATUSES),
                SelectFilter::make('priority')->options(ItRequest::PRIORITIES),
                SelectFilter::make('category')->options(ItRequest::CATEGORIES),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),
                SelectFilter::make('assigned_to_employee_id')
                    ->label('Assigned To')
                    ->relationship('assignedToEmployee', 'full_name'),
            ])
            ->actions([
                \Filament\Actions\Action::make('assign_to_me')
                    ->label('Assign to Me')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (ItRequest $record) => ! $record->assigned_to_employee_id)
                    ->action(function (ItRequest $record) {
                        $employee = \Modules\HR\Models\Employee::where('user_id', auth()->id())->first();
                        if ($employee) {
                            $record->update(['assigned_to_employee_id' => $employee->id, 'status' => 'in_progress']);
                        }
                    }),
                \Filament\Actions\Action::make('resolve')
                    ->label('Mark Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ItRequest $record) => in_array($record->status, ['open', 'in_progress', 'pending_info']))
                    ->form([
                        Textarea::make('resolution_notes')->label('Resolution Notes')->required()->rows(3),
                    ])
                    ->action(fn (ItRequest $record, array $data) => $record->update([
                        'status'           => 'resolved',
                        'resolved_at'      => now(),
                        'resolution_notes' => $data['resolution_notes'],
                    ])),
                \Filament\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->visible(fn (ItRequest $record) => $record->status === 'resolved')
                    ->action(fn (ItRequest $record) => $record->update(['status' => 'closed'])),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListItRequests::route('/'),
            'create' => Pages\CreateItRequest::route('/create'),
            'view'   => Pages\ViewItRequest::route('/{record}'),
            'edit'   => Pages\EditItRequest::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'subject'];
    }
}
