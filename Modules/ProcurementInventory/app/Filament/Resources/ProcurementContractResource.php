<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\ProcurementContractResource\Pages;
use Modules\ProcurementInventory\Filament\Resources\ProcurementContractResource\RelationManagers\ContractLinesRelationManager;
use Modules\ProcurementInventory\Models\ProcurementContract;
use Modules\ProcurementInventory\Models\Vendor;

class ProcurementContractResource extends Resource
{
    protected static ?string $model = ProcurementContract::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'Contracts';

    protected static ?int $navigationSort = 12;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contract Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('vendor_id')
                        ->label('Vendor')
                        ->options(function () {
                            $companyId = filament()->getTenant()?->id ?? auth()->user()?->current_company_id;
                            return Vendor::query()
                                ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
                                ->where('status', 'active')
                                ->pluck('name', 'id');
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('contract_number')
                        ->disabled()
                        ->placeholder('Auto-generated'),

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('contract_type')
                        ->options([
                            'blanket_order'     => 'Blanket Order',
                            'framework'         => 'Framework',
                            'fixed_price'       => 'Fixed Price',
                            'rate_contract'     => 'Rate Contract',
                            'service_agreement' => 'Service Agreement',
                        ])
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->options([
                            'draft'      => 'Draft',
                            'active'     => 'Active',
                            'expired'    => 'Expired',
                            'terminated' => 'Terminated',
                            'suspended'  => 'Suspended',
                        ])
                        ->default('draft')
                        ->required(),

                    Forms\Components\DatePicker::make('start_date')->required(),
                    Forms\Components\DatePicker::make('end_date')->required(),

                    Forms\Components\TextInput::make('total_value')
                        ->numeric()
                        ->prefix('GHS')
                        ->label('Total Value / Commitment Cap')
                        ->nullable(),

                    Forms\Components\TextInput::make('currency')
                        ->default('GHS')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('payment_terms')
                        ->numeric()
                        ->suffix('days')
                        ->nullable(),
                ]),

            Section::make('Renewal')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('auto_renewal')
                        ->default(false)
                        ->live(),

                    Forms\Components\TextInput::make('renewal_notice_days')
                        ->numeric()
                        ->default(30)
                        ->suffix('days before expiry')
                        ->visible(fn ($get) => $get('auto_renewal')),
                ]),

            Section::make('Documents & Notes')
                ->columns(1)
                ->schema([
                    Forms\Components\FileUpload::make('file_path')
                        ->label('Contract Document')
                        ->disk('public')
                        ->directory('procurement/contracts')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->nullable(),

                    Forms\Components\Textarea::make('notes')->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->searchable()
                    ->weight('bold')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contract_type')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->end_date?->isPast() ? 'danger' : null),

                Tables\Columns\TextColumn::make('total_value')
                    ->money('GHS')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('committed_value')
                    ->money('GHS'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'      => 'gray',
                        'active'     => 'success',
                        'expired'    => 'warning',
                        'terminated' => 'danger',
                        'suspended'  => 'warning',
                        default      => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'      => 'Draft',
                        'active'     => 'Active',
                        'expired'    => 'Expired',
                        'terminated' => 'Terminated',
                        'suspended'  => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name'),
            ])
            ->actions([
                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (ProcurementContract $record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(function (ProcurementContract $record) {
                        $record->update(['status' => 'active']);
                        Notification::make()->title('Contract activated')->success()->send();
                    }),

                Action::make('terminate')
                    ->label('Terminate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ProcurementContract $record) => $record->status === 'active')
                    ->form([
                        Forms\Components\Textarea::make('termination_notes')
                            ->label('Reason for Termination')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (ProcurementContract $record, array $data) {
                        $record->update([
                            'status' => 'terminated',
                            'notes'  => ($record->notes ? $record->notes . "\n\n" : '') . "Terminated: {$data['termination_notes']}",
                        ]);
                        Notification::make()->title('Contract terminated')->warning()->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (ProcurementContract $record) => $record->status === 'draft'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ContractLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProcurementContracts::route('/'),
            'create' => Pages\CreateProcurementContract::route('/create'),
            'edit'   => Pages\EditProcurementContract::route('/{record}/edit'),
            'view'   => Pages\ViewProcurementContract::route('/{record}'),
        ];
    }
}