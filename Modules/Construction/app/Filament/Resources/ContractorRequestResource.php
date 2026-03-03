<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Construction\Filament\Resources\ContractorRequestResource\Pages;
use Modules\Construction\Models\ContractorRequest;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\Contractor;
use Modules\Construction\Models\ProjectPhase;
use Modules\ProcurementInventory\Models\Item;

class ContractorRequestResource extends Resource
{
    protected static ?string $model = ContractorRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'Contractor Requests';

    public static function getNavigationBadge(): ?string
    {
        return (string) ContractorRequest::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->options(fn () => ConstructionProject::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                    Select::make('contractor_id')
                        ->label('Contractor')
                        ->options(fn () => Contractor::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    Select::make('project_phase_id')
                        ->label('Phase')
                        ->options(fn () => ProjectPhase::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable(),
                    Select::make('request_type')
                        ->options(array_combine(
                            ContractorRequest::REQUEST_TYPES,
                            array_map('ucfirst', ContractorRequest::REQUEST_TYPES)
                        ))
                        ->default('materials')
                        ->required()
                        ->live(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                    Select::make('priority')
                        ->options([
                            'low'    => 'Low',
                            'medium' => 'Medium',
                            'high'   => 'High',
                            'urgent' => 'Urgent',
                        ])
                        ->default('medium')
                        ->required(),
                    DatePicker::make('required_by')->label('Required By'),
                ]),
                TextInput::make('requested_amount')
                    ->numeric()
                    ->prefix('GHS')
                    ->visible(fn (\Filament\Forms\Get $get) => in_array($get('request_type'), ['funds', 'equipment'])),
                Textarea::make('description')->required()->rows(4)->columnSpanFull(),
            ]),

            Section::make('Line Items')
                ->visible(fn (\Filament\Forms\Get $get) => in_array($get('request_type'), ['materials', 'equipment']))
                ->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('item_id')
                                    ->label('Inventory Item')
                                    ->options(fn () => Item::pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->nullable(),
                                TextInput::make('material_name')->required()->maxLength(255),
                            ]),
                            Grid::make(4)->schema([
                                TextInput::make('unit')->maxLength(20),
                                TextInput::make('quantity')->numeric()->default(1)->required()->minValue(0.001),
                                TextInput::make('unit_cost')->numeric()->prefix('GHS'),
                                TextInput::make('total_cost')->numeric()->prefix('GHS')->readOnly(),
                            ]),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(40),
                TextColumn::make('contractor.name')->label('Contractor')->searchable(),
                TextColumn::make('project.name')->label('Project')->searchable(),
                TextColumn::make('request_type')->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('priority')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved'     => 'success',
                        'rejected'     => 'danger',
                        'fulfilled'    => 'success',
                        'under_review' => 'warning',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('requested_amount')->money('GHS')->placeholder('—'),
                TextColumn::make('required_by')->date()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(
                        ContractorRequest::STATUSES,
                        array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), ContractorRequest::STATUSES))
                    )),
                SelectFilter::make('request_type')
                    ->options(array_combine(
                        ContractorRequest::REQUEST_TYPES,
                        array_map('ucfirst', ContractorRequest::REQUEST_TYPES)
                    )),
                SelectFilter::make('priority')
                    ->options(array_combine(
                        ContractorRequest::PRIORITIES,
                        array_map('ucfirst', ContractorRequest::PRIORITIES)
                    )),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                TableAction::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review']))
                    ->form([
                        TextInput::make('approved_amount')->numeric()->prefix('GHS')->required()->label('Approved Amount'),
                        Textarea::make('approval_notes')->rows(3)->label('Notes'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->approve((float) $data['approved_amount'], $data['approval_notes'] ?? '');
                    }),
                TableAction::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'under_review']))
                    ->form([
                        Textarea::make('approval_notes')->rows(3)->label('Reason')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->reject($data['approval_notes']);
                    }),
                TableAction::make('mark_fulfilled')
                    ->label('Mark Fulfilled')
                    ->icon('heroicon-o-trophy')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->action(fn ($record) => $record->update(['status' => 'fulfilled'])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContractorRequests::route('/'),
            'create' => Pages\CreateContractorRequest::route('/create'),
            'view'   => Pages\ViewContractorRequest::route('/{record}'),
            'edit'   => Pages\EditContractorRequest::route('/{record}/edit'),
        ];
    }
}
