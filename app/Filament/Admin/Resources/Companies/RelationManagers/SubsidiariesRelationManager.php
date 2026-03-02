<?php

namespace App\Filament\Admin\Resources\Companies\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SubsidiariesRelationManager extends RelationManager
{
    protected static string $relationship = 'childCompanies';

    protected static ?string $title = 'Subsidiaries';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) =>
                        $operation === 'create' ? $set('slug', str($state)->slug()) : null
                    ),

                TextInput::make('slug')
                    ->readOnly()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->dehydrated(true),
            ]),

            Grid::make(2)->schema([
                Select::make('company_service_type')
                    ->label('Primary Service Type')
                    ->options([
                        'general'       => 'General Business',
                        'hostel'        => 'Hostel',
                        'farm'          => 'Farm',
                        'manufacturing' => 'Manufacturing',
                    ])
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('company_service_type')
                    ->label('Service Type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('child_companies_count')
                    ->label('Sub-subsidiaries')
                    ->counts('childCompanies')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['type'] = 'subsidiary';
                        return $data;
                    }),
            ])
            ->actions([
                ViewAction::make(),
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
