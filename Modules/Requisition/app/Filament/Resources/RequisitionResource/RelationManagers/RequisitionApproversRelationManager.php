<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Requisition\Events\RequisitionShared;

class RequisitionApproversRelationManager extends RelationManager
{
    protected static string $relationship = 'approvers';

    protected static ?string $title = 'Reviewers & Approvers';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('role')
                    ->options(['reviewer' => 'Reviewer', 'approver' => 'Approver'])
                    ->default('reviewer')
                    ->required(),
            ]),
            Grid::make(2)->schema([
                Select::make('decision')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'commented' => 'Commented'])
                    ->default('pending'),
            ]),
            Textarea::make('comment')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.full_name')->label('Employee')->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn ($state) => $state === 'approver' ? 'success' : 'info'),
                TextColumn::make('decision')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'approved'  => 'success',
                        'rejected'  => 'danger',
                        'commented' => 'warning',
                        default     => 'gray',
                    }),
                TextColumn::make('decided_at')->dateTime()->label('Decided At'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function ($record) {
                        event(new RequisitionShared($this->getOwnerRecord(), $record));
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
