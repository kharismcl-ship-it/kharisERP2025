<?php

namespace Modules\Construction\Filament\Resources\ConstructionProjectResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Construction\Models\ContractorRequest;
use Modules\Construction\Models\Contractor;

class ContractorRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'contractorRequests';

    protected static ?string $title = 'Contractor Requests';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('contractor_id')
                ->label('Contractor')
                ->options(fn () => Contractor::pluck('name', 'id')->toArray())
                ->searchable()
                ->required(),
            Select::make('request_type')
                ->options(array_combine(
                    ContractorRequest::REQUEST_TYPES,
                    array_map('ucfirst', ContractorRequest::REQUEST_TYPES)
                ))
                ->default('materials')
                ->required(),
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Select::make('priority')
                ->options([
                    'low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent',
                ])
                ->default('medium')
                ->required(),
            Textarea::make('description')->required()->rows(3)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->limit(35),
                TextColumn::make('contractor.name')->label('Contractor'),
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
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'fulfilled' => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
            ])
            ->headerActions([\Filament\Tables\Actions\CreateAction::make()])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([\Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ])])
            ->defaultSort('created_at', 'desc');
    }
}
