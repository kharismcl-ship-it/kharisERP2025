<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Requisition\Filament\Resources\RequisitionResource\Pages;
use Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;
use Modules\Requisition\Models\Requisition;

class RequisitionResource extends Resource
{
    protected static ?string $model = Requisition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')->schema([
                Grid::make(2)->schema([
                    Select::make('requester_employee_id')
                        ->label('Requester Employee')
                        ->relationship('requesterEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('target_company_id')
                        ->label('Target Company')
                        ->relationship('targetCompany', 'name')
                        ->searchable()
                        ->preload(),
                ]),
                Grid::make(2)->schema([
                    Select::make('target_department_id')
                        ->label('Target Department')
                        ->relationship('targetDepartment', 'name')
                        ->searchable()
                        ->preload(),
                    Select::make('request_type')
                        ->options(Requisition::TYPES)
                        ->required()
                        ->default('general'),
                ]),
                Grid::make(2)->schema([
                    Select::make('urgency')
                        ->options(Requisition::URGENCIES)
                        ->required()
                        ->default('medium'),
                    TextInput::make('title')->required()->maxLength(255),
                ]),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Budget')->schema([
                Grid::make(2)->schema([
                    Select::make('cost_centre_id')
                        ->label('Cost Centre')
                        ->relationship('costCentre', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make('total_estimated_cost')
                        ->label('Total Estimated Cost')
                        ->numeric()
                        ->prefix('GHS'),
                ]),
            ]),

            Section::make('Status & Resolution')->schema([
                Grid::make(2)->schema([
                    Select::make('status')
                        ->options(Requisition::STATUSES)
                        ->default('draft')
                        ->required(),
                    Select::make('approved_by')
                        ->label('Approved By')
                        ->relationship('approvedByUser', 'name')
                        ->searchable()
                        ->nullable(),
                ]),
                Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->rows(2)
                    ->columnSpanFull()
                    ->visible(fn ($get) => in_array($get('status'), ['rejected'])),
                Grid::make(2)->schema([
                    DateTimePicker::make('approved_at')->label('Approved At')->nullable(),
                    DateTimePicker::make('fulfilled_at')->label('Fulfilled At')->nullable(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('request_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Requisition::TYPES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'fund'      => 'warning',
                        'material'  => 'info',
                        'equipment' => 'success',
                        'service'   => 'gray',
                        default     => 'primary',
                    }),
                TextColumn::make('targetCompany.name')->label('Target Company'),
                TextColumn::make('urgency')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        default  => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft'        => 'gray',
                        'submitted'    => 'info',
                        'under_review' => 'warning',
                        'approved'     => 'success',
                        'rejected'     => 'danger',
                        'fulfilled'    => 'success',
                        default        => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(Requisition::STATUSES),
                SelectFilter::make('request_type')->options(Requisition::TYPES),
                SelectFilter::make('urgency')->options(Requisition::URGENCIES),
                Filter::make('created_today')
                    ->label('Created Today')
                    ->query(fn ($query) => $query->whereDate('created_at', today())),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->visible(fn (Requisition $record) => $record->status === 'draft')
                    ->action(fn (Requisition $record) => $record->update(['status' => 'submitted'])),
                \Filament\Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Requisition $record) => in_array($record->status, ['submitted', 'under_review']))
                    ->action(fn (Requisition $record) => $record->update(['status' => 'approved', 'approved_at' => now(), 'approved_by' => auth()->id()])),
                \Filament\Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Requisition $record) => in_array($record->status, ['submitted', 'under_review', 'approved']))
                    ->form([
                        Textarea::make('rejection_reason')->label('Rejection Reason')->required()->rows(3),
                    ])
                    ->action(fn (Requisition $record, array $data) => $record->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ])),
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
        return [
            RelationManagers\RequisitionItemsRelationManager::class,
            RelationManagers\RequisitionApproversRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitions::route('/'),
            'create' => Pages\CreateRequisition::route('/create'),
            'view'   => Pages\ViewRequisition::route('/{record}'),
            'edit'   => Pages\EditRequisition::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title'];
    }
}
