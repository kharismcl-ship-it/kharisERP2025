<?php

namespace Modules\Hostels\Filament\Resources\HostelResource\RelationManagers;

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

class HostelStaffAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'employeeCompanyAssignments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('role')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('assigned_at'),
                Forms\Components\DateTimePicker::make('expires_at'),
                Forms\Components\Textarea::make('assignment_reason')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query, RelationManager $livewire) => $query->where('company_id', $livewire->getOwnerRecord()->company_id))
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable(),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignment_reason')
                    ->searchable()
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        // Set company_id to the current hostel's company
                        $data['company_id'] = $livewire->getOwnerRecord()->company_id;
                        // Map fields
                        if (isset($data['assigned_at'])) {
                            $data['start_date'] = date('Y-m-d', strtotime($data['assigned_at']));
                        } else {
                            $data['start_date'] = now()->toDateString();
                        }
                        $data['is_active'] = true;

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        // Map fields
                        if (isset($data['assigned_at'])) {
                            $data['start_date'] = date('Y-m-d', strtotime($data['assigned_at']));
                        }

                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Staff Assignments';
    }

    public static function getLabel(): string
    {
        return 'Staff Assignment';
    }
}
