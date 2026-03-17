<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubordinatesRelationManager extends RelationManager
{
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool { return true; }

    protected static string $relationship = 'subordinates';

    protected static ?string $title = 'Direct Reports';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('employee_photo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&background=random')
                    ->size(36),
                Tables\Columns\TextColumn::make('employee_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('jobPosition.title')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'full_time' => 'primary',
                        'part_time' => 'success',
                        'contract'  => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract'  => 'Contract',
                        'intern'    => 'Intern',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'probation' => 'warning',
                        default     => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_status')
                    ->options([
                        'active'     => 'Active',
                        'probation'  => 'Probation',
                        'suspended'  => 'Suspended',
                        'terminated' => 'Terminated',
                        'resigned'   => 'Resigned',
                    ]),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->label('Add Direct Report')
                    ->recordSelectSearchColumns(['first_name', 'last_name', 'employee_code', 'email'])
                    ->recordTitle(fn (Model $record): string => "{$record->employee_code} — {$record->full_name}")
                    ->preloadRecordSelect()
                    ->associateAnother(false),
            ])
            ->actions([
                DissociateAction::make()
                    ->label('Remove')
                    ->modalHeading('Remove Direct Report')
                    ->modalDescription(fn (Model $record): string => "Remove {$record->full_name} from this manager's direct reports? The employee record will not be deleted."),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make()
                        ->label('Remove Selected'),
                ]),
            ]);
    }
}
