<?php

namespace App\Filament\CompanyAdmin\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // Scope to users who belong to the current company via the pivot
            ->modifyQueryUsing(function (Builder $query): Builder {
                $tenant = Filament::getTenant();

                if (! $tenant) {
                    return $query;
                }

                return $query->whereHas(
                    'companies',
                    fn (Builder $q) => $q->where('companies.id', $tenant->getKey())
                );
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email Address')
                    ->searchable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => str($state)->headline()->toString())
                    ->placeholder('—'),

                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->state(fn ($record): bool => (bool) $record->email_verified_at)
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}