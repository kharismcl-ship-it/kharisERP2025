<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Requisition\Events\RequisitionShared;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class RequisitionApproversRelationManager extends RelationManager
{
    protected static string $relationship = 'approvers';

    protected static ?string $title = 'Reviewers & Approvers';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([
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
                TextInput::make('order')
                    ->label('Sequence Order')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->helperText('Lower number = notified first'),
            ]),
            Grid::make(2)->schema([
                Select::make('decision')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'commented' => 'Commented'])
                    ->default('pending'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]),
            Textarea::make('comment')->rows(2)->columnSpanFull(),
            SignaturePad::make('signature')
                ->label('Approver Signature')
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')->label('#')->sortable(),
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
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('decided_at')->dateTime()->label('Decided At'),
            ])
            ->defaultSort('order')
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