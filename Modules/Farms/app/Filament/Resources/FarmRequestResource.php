<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\FarmRequestResource\Pages;
use Modules\Farms\Filament\Resources\FarmRequestResource\RelationManagers;
use Modules\Farms\Models\FarmRequest;
use Modules\Farms\Models\FarmWorker;

class FarmRequestResource extends Resource
{
    protected static ?string $model = FarmRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Farm Requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),

                    Select::make('requested_by')
                        ->label('Requested By (Worker)')
                        ->options(function ($get) {
                            $farmId = $get('farm_id');
                            if (! $farmId) {
                                return FarmWorker::pluck('name', 'id');
                            }
                            return FarmWorker::where('farm_id', $farmId)
                                ->get()
                                ->pluck('display_name', 'id');
                        })
                        ->searchable()
                        ->nullable(),

                    Select::make('request_type')
                        ->label('Request Type')
                        ->options([
                            'materials' => 'Materials',
                            'funds'     => 'Funds',
                            'equipment' => 'Equipment',
                            'services'  => 'Services',
                            'labour'    => 'Labour',
                            'other'     => 'Other',
                        ])
                        ->default('materials')
                        ->required(),

                    Select::make('urgency')
                        ->options([
                            'low'    => 'Low',
                            'medium' => 'Medium',
                            'high'   => 'High',
                            'urgent' => 'Urgent',
                        ])
                        ->default('medium')
                        ->required(),

                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ]),

            Section::make('Status & Approval')
                ->collapsible()
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'draft'     => 'Draft',
                            'submitted' => 'Submitted',
                            'approved'  => 'Approved',
                            'rejected'  => 'Rejected',
                            'fulfilled' => 'Fulfilled',
                        ])
                        ->default('draft')
                        ->required()
                        ->live(),

                    TextInput::make('approved_by')
                        ->label('Approved By (User ID)')
                        ->numeric()
                        ->nullable(),

                    Textarea::make('rejection_reason')
                        ->label('Rejection Reason')
                        ->rows(3)
                        ->columnSpanFull()
                        ->visible(fn ($get) => $get('status') === 'rejected'),

                    Textarea::make('notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('request_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color('primary'),
                TextColumn::make('farm.name')->label('Farm')->sortable(),
                TextColumn::make('urgency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'submitted' => 'warning',
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'fulfilled' => 'info',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'draft'     => 'Draft',
                    'submitted' => 'Submitted',
                    'approved'  => 'Approved',
                    'rejected'  => 'Rejected',
                    'fulfilled' => 'Fulfilled',
                ]),
                SelectFilter::make('request_type')->label('Type')->options([
                    'materials' => 'Materials',
                    'funds'     => 'Funds',
                    'equipment' => 'Equipment',
                    'services'  => 'Services',
                    'labour'    => 'Labour',
                    'other'     => 'Other',
                ]),
                SelectFilter::make('urgency')->options([
                    'urgent' => 'Urgent',
                    'high'   => 'High',
                    'medium' => 'Medium',
                    'low'    => 'Low',
                ]),
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'submitted')
                    ->action(fn ($record) => $record->update([
                        'status'      => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'submitted')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(fn ($record, array $data) => $record->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                    ])),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\FarmRequestItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmRequests::route('/'),
            'create' => Pages\CreateFarmRequest::route('/create'),
            'view'   => Pages\ViewFarmRequest::route('/{record}'),
            'edit'   => Pages\EditFarmRequest::route('/{record}/edit'),
        ];
    }
}
