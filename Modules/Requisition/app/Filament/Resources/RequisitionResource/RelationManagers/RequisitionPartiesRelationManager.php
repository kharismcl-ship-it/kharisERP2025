<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Requisition\Events\RequisitionPartyAdded;
use Modules\Requisition\Models\RequisitionActivity;
use Modules\Requisition\Models\RequisitionParty;

class RequisitionPartiesRelationManager extends RelationManager
{
    protected static string $relationship = 'parties';

    protected static ?string $title = 'Notify Parties';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('party_type')
                    ->options(['employee' => 'Individual Employee', 'department' => 'Entire Department'])
                    ->default('employee')
                    ->required()
                    ->live(),

                Select::make('reason')
                    ->options(RequisitionParty::REASONS)
                    ->default('for_info')
                    ->required(),
            ]),

            Select::make('employee_id')
                ->label('Employee')
                ->relationship('employee', 'full_name')
                ->searchable()
                ->preload()
                ->visible(fn (Get $get) => $get('party_type') === 'employee')
                ->nullable(),

            Select::make('department_id')
                ->label('Department')
                ->relationship('department', 'name')
                ->searchable()
                ->preload()
                ->visible(fn (Get $get) => $get('party_type') === 'department')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('party_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => $state === 'employee' ? 'info' : 'warning'),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->default('—'),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->default('—'),
                TextColumn::make('reason')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RequisitionParty::REASONS[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'for_approval' => 'success',
                        'for_action'   => 'warning',
                        default        => 'gray',
                    }),
                TextColumn::make('notified_at')
                    ->label('Notified At')
                    ->dateTime()
                    ->default('Pending'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($record) {
                        $owner = $this->getOwnerRecord();
                        RequisitionActivity::log(
                            $owner,
                            'party_added',
                            $record->party_type === 'employee'
                                ? "Party added (employee): {$record->employee?->full_name} — " . (RequisitionParty::REASONS[$record->reason] ?? $record->reason)
                                : "Party added (department): {$record->department?->name} — " . (RequisitionParty::REASONS[$record->reason] ?? $record->reason),
                        );
                        event(new RequisitionPartyAdded($owner, $record));
                    }),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}