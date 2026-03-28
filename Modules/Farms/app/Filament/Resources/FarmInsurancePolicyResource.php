<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmInsurancePolicyResource\Pages;
use Modules\Farms\Models\FarmInsurancePolicy;

class FarmInsurancePolicyResource extends Resource
{
    protected static ?string $model = FarmInsurancePolicy::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Crop Insurance';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Policy Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('policy_number')->required()->maxLength(100),

                    TextInput::make('insurer_name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g. GAIP, SIC Insurance, Enterprise Insurance'),

                    Select::make('insurance_type')
                        ->options([
                            'weather_index'    => 'Weather Index',
                            'multi_peril_crop' => 'Multi-Peril Crop',
                            'livestock'        => 'Livestock',
                            'property'         => 'Property',
                        ])
                        ->default('weather_index')
                        ->required(),

                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle (optional)')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->nullable(),

                    Select::make('livestock_batch_id')
                        ->label('Livestock Batch (optional)')
                        ->relationship('livestockBatch', 'batch_name')
                        ->searchable()
                        ->nullable(),

                    TextInput::make('covered_crop')->maxLength(100),
                    TextInput::make('covered_area_ha')->label('Covered Area (ha)')->numeric()->step(0.0001)->suffix('ha'),
                ]),

            Section::make('Financial Terms')
                ->columns(2)
                ->schema([
                    TextInput::make('sum_insured')->label('Sum Insured (GHS)')->numeric()->prefix('GHS')->required(),
                    TextInput::make('premium_amount')->label('Premium (GHS)')->numeric()->prefix('GHS')->required(),
                    DatePicker::make('premium_paid_date')->label('Premium Paid Date'),
                    DatePicker::make('start_date')->required(),
                    DatePicker::make('end_date')->required(),
                    Select::make('status')
                        ->options([
                            'active'    => 'Active',
                            'expired'   => 'Expired',
                            'claimed'   => 'Claimed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('active')
                        ->required(),
                ]),

            Section::make('Trigger & Notes')
                ->schema([
                    Textarea::make('trigger_description')
                        ->label('Trigger Description')
                        ->rows(3)
                        ->placeholder('e.g. Claim triggered if rainfall < 400mm during 60-day growing period')
                        ->columnSpanFull(),

                    FileUpload::make('document_path')
                        ->label('Policy Document')
                        ->directory('farm-insurance')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->columnSpanFull(),
                ]),

            Section::make('Claim Information')
                ->columns(2)
                ->visible(fn (Get $get): bool => in_array($get('status'), ['claimed', 'expired', 'cancelled']))
                ->schema([
                    TextInput::make('claim_amount')->label('Claim Amount (GHS)')->numeric()->prefix('GHS')->nullable(),
                    DatePicker::make('claim_date')->nullable(),
                    Select::make('claim_status')
                        ->options([
                            'pending'  => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'paid'     => 'Paid',
                        ])
                        ->nullable(),
                    Textarea::make('claim_notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('policy_number')->searchable()->copyable(),
                TextColumn::make('insurer_name')->searchable()->toggleable(),
                TextColumn::make('insurance_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('covered_crop')->placeholder('—')->toggleable(),
                TextColumn::make('sum_insured')->money('GHS')->label('Sum Insured')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'expired'   => 'gray',
                        'claimed'   => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('claim_status')
                    ->label('Claim')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'paid'     => 'success',
                        'rejected' => 'danger',
                        'pending'  => 'warning',
                        default    => 'gray',
                    })
                    ->placeholder('—'),
                TextColumn::make('end_date')->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'active'    => 'Active',
                    'expired'   => 'Expired',
                    'claimed'   => 'Claimed',
                    'cancelled' => 'Cancelled',
                ]),
            ])
            ->actions([
                Action::make('file_claim')
                    ->label('File Claim')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->visible(fn (FarmInsurancePolicy $record): bool => $record->status === 'active')
                    ->form([
                        TextInput::make('claim_amount')->label('Claim Amount (GHS)')->numeric()->prefix('GHS')->required(),
                        DatePicker::make('claim_date')->required()->default(today()),
                        Textarea::make('claim_notes')->rows(2),
                    ])
                    ->action(function (FarmInsurancePolicy $record, array $data): void {
                        $record->update([
                            'status'       => 'claimed',
                            'claim_amount' => $data['claim_amount'],
                            'claim_date'   => $data['claim_date'],
                            'claim_notes'  => $data['claim_notes'],
                            'claim_status' => 'pending',
                        ]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('end_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmInsurancePolicies::route('/'),
            'create' => Pages\CreateFarmInsurancePolicy::route('/create'),
            'edit'   => Pages\EditFarmInsurancePolicy::route('/{record}/edit'),
        ];
    }
}