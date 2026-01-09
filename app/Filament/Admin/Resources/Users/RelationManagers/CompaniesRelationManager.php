<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompaniesRelationManager extends RelationManager
{
    protected static string $relationship = 'companies';

    protected static ?string $modelLabel = 'Company Assignment';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Company Assignments';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('pivot.position')
                    ->label('Position')
                    ->nullable(),
                Forms\Components\Toggle::make('pivot.is_active')
                    ->label('Active')
                    ->default(true),
                Forms\Components\DatePicker::make('pivot.assigned_at')
                    ->label('Assigned At')
                    ->default(now()),
                Forms\Components\DatePicker::make('pivot.expires_at')
                    ->label('Expires At')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.position')
                    ->label('Position')
                    ->sortable(),
                Tables\Columns\IconColumn::make('pivot.is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('pivot.assigned_at')
                    ->label('Assigned At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pivot.expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
