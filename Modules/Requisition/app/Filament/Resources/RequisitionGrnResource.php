<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionGrnResource\Pages;
use Modules\Requisition\Filament\Resources\RequisitionGrnResource\RelationManagers\GrnLinesRelationManager;
use Modules\Requisition\Models\RequisitionGrn;

class RequisitionGrnResource extends Resource
{
    protected static ?string $model = RequisitionGrn::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Goods Receipts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Receipt Details')->schema([
                Grid::make(2)->schema([
                    Select::make('requisition_id')
                        ->label('Linked Requisition')
                        ->relationship('requisition', 'reference')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Select::make('purchase_order_id')
                        ->label('Purchase Order')
                        ->relationship('purchaseOrder', 'po_number')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    Select::make('received_by_employee_id')
                        ->label('Received By')
                        ->relationship('receivedByEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    DatePicker::make('received_date')->required()->default(now()),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('supplier_delivery_ref')->label("Supplier's Delivery Ref")->nullable()->maxLength(255),
                    Select::make('status')
                        ->options([
                            'draft'              => 'Draft',
                            'submitted'          => 'Submitted',
                            'accepted'           => 'Accepted',
                            'partially_accepted' => 'Partially Accepted',
                            'rejected'           => 'Rejected',
                        ])
                        ->default('draft')
                        ->required(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grn_number')->label('GRN #')->badge()->searchable(),
                TextColumn::make('requisition.reference')->label('Requisition')->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'              => 'gray',
                        'submitted'          => 'info',
                        'accepted'           => 'success',
                        'partially_accepted' => 'warning',
                        'rejected'           => 'danger',
                        default              => 'gray',
                    }),
                TextColumn::make('received_date')->date(),
                TextColumn::make('receivedByEmployee.full_name')->label('Received By')->placeholder('—'),
                TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('accept_grn')
                    ->label('Accept')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'submitted')
                    ->requiresConfirmation()
                    ->modalHeading('Accept Goods Receipt?')
                    ->action(function ($record) {
                        $record->accept();
                        Notification::make()->success()->title('GRN accepted and fulfillment updated.')->send();
                    }),
                Action::make('reject_grn')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'submitted')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->danger()->title('GRN rejected.')->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            GrnLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionGrns::route('/'),
            'create' => Pages\CreateRequisitionGrn::route('/create'),
            'edit'   => Pages\EditRequisitionGrn::route('/{record}/edit'),
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