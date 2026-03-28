<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionRfqResource\Pages;
use Modules\Requisition\Filament\Resources\RequisitionRfqResource\RelationManagers\RfqBidsRelationManager;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionRfq;

class RequisitionRfqResource extends Resource
{
    protected static ?string $model = RequisitionRfq::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'RFQ / Bids';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('RFQ Details')->schema([
                Grid::make(2)->schema([
                    Select::make('requisition_id')
                        ->label('Linked Requisition')
                        ->options(function () {
                            $tenant = filament()->getTenant();
                            if (! $tenant) return [];
                            return Requisition::where('company_id', $tenant->getKey())
                                ->whereIn('status', ['approved', 'fulfilled'])
                                ->pluck('reference', 'id');
                        })
                        ->searchable()
                        ->required(),
                    TextInput::make('title')->required()->maxLength(255),
                ]),
                Textarea::make('description')->rows(2)->columnSpanFull(),
                Grid::make(2)->schema([
                    DatePicker::make('deadline')->label('Bid Deadline')->nullable(),
                    Select::make('status')
                        ->options([
                            'draft'      => 'Draft',
                            'sent'       => 'Sent',
                            'evaluating' => 'Evaluating',
                            'awarded'    => 'Awarded',
                            'cancelled'  => 'Cancelled',
                        ])
                        ->default('draft')
                        ->required()
                        ->live(),
                ]),
            ]),

            Section::make('Award Decision')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('awarded_vendor_id')
                            ->label('Awarded Vendor')
                            ->relationship('awardedVendor', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        DatePicker::make('awarded_at')->label('Award Date')->nullable(),
                    ]),
                    Textarea::make('award_justification')->label('Justification')->rows(2)->columnSpanFull(),
                ])
                ->visible(fn (\Filament\Schemas\Components\Component $component) => in_array(
                    $component->getLivewire()->data['status'] ?? '',
                    ['evaluating', 'awarded']
                ))
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rfq_number')
                    ->label('RFQ #')
                    ->badge()
                    ->searchable(),
                TextColumn::make('title')->limit(40)->searchable(),
                TextColumn::make('requisition.reference')->label('Requisition')->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'      => 'gray',
                        'sent'       => 'info',
                        'evaluating' => 'warning',
                        'awarded'    => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),
                TextColumn::make('bids_count')
                    ->label('Bids')
                    ->counts('bids'),
                TextColumn::make('deadline')->date()->placeholder('—'),
                TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([EditAction::make(), ViewAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            RfqBidsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionRfqs::route('/'),
            'create' => Pages\CreateRequisitionRfq::route('/create'),
            'edit'   => Pages\EditRequisitionRfq::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query  = parent::getEloquentQuery();
        $tenant = filament()->getTenant();

        if ($tenant) {
            $query->where('company_id', $tenant->getKey());
        }

        return $query;
    }
}