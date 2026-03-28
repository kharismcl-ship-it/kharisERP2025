<?php

namespace Modules\ProcurementInventory\Filament\Resources\VendorResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VendorCertificatesRelationManager extends RelationManager
{
    protected static string $relationship = 'certificates';

    protected static ?string $title = 'Certificates';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('certificate_type')
                ->options([
                    'trade_license' => 'Trade License',
                    'tax_clearance' => 'Tax Clearance',
                    'insurance'     => 'Insurance',
                    'iso_9001'      => 'ISO 9001',
                    'iso_14001'     => 'ISO 14001',
                    'soncap'        => 'SONCAP',
                    'nafdac'        => 'NAFDAC',
                    'other'         => 'Other',
                ])
                ->required(),

            Forms\Components\TextInput::make('certificate_number')
                ->maxLength(255)
                ->nullable(),

            Forms\Components\TextInput::make('issuing_authority')
                ->maxLength(255)
                ->nullable(),

            Forms\Components\DatePicker::make('issue_date')->nullable(),

            Forms\Components\DatePicker::make('expiry_date')->nullable(),

            Forms\Components\FileUpload::make('file_path')
                ->label('Certificate Document')
                ->disk('public')
                ->directory('procurement/certificates')
                ->nullable(),

            Forms\Components\Textarea::make('notes')->rows(2)->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('certificate_type')
                    ->badge()
                    ->color('info')
                    ->label('Type'),

                Tables\Columns\TextColumn::make('certificate_number')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('issuing_authority')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->isExpired()
                        ? 'danger'
                        : ($record->isExpiringSoon() ? 'warning' : null)),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'valid'           => 'success',
                        'expiring_soon'   => 'warning',
                        'expired'         => 'danger',
                        'pending_renewal' => 'info',
                        default           => 'gray',
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}