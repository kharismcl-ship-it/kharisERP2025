<?php

namespace Modules\HR\Filament\Resources\Staff\MyCertificationResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class CertificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Certification')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('issuing_authority')
                    ->label('Issuing Authority')
                    ->searchable(),
                Tables\Columns\TextColumn::make('certificate_number')
                    ->label('Certificate No.')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->placeholder('No Expiry')
                    ->color(fn ($record) => $record?->is_expired ? 'danger' : ($record?->is_expiring_soon ? 'warning' : null))
                    ->sortable(),
                Tables\Columns\IconColumn::make('certificate_path')
                    ->label('Document')
                    ->boolean()
                    ->getStateUsing(fn ($record) => (bool) $record->certificate_path),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
