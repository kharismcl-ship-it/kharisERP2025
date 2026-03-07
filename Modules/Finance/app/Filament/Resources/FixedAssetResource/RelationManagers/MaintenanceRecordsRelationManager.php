<?php

namespace Modules\Finance\Filament\Resources\FixedAssetResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Modules\Finance\Models\FixedAssetMaintenanceRecord;

class MaintenanceRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceRecords';

    protected static ?string $title = 'Maintenance & Service Records';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g. Annual Engine Service, Safety Inspection')
                ->columnSpanFull(),

            Select::make('maintenance_type')
                ->label('Type')
                ->options(FixedAssetMaintenanceRecord::TYPES)
                ->default('preventive')
                ->required(),

            Select::make('status')
                ->options(FixedAssetMaintenanceRecord::STATUSES)
                ->default('scheduled')
                ->required(),

            DatePicker::make('scheduled_date')->label('Scheduled Date')->nullable(),
            DatePicker::make('completed_date')->label('Completed Date')->nullable(),

            TextInput::make('cost')
                ->label('Cost (GHS)')
                ->numeric()
                ->prefix('GHS')
                ->nullable(),

            TextInput::make('contractor')
                ->label('Contractor / Vendor')
                ->maxLength(255)
                ->nullable(),

            DatePicker::make('next_due_date')->label('Next Due Date')->nullable(),

            Textarea::make('description')->rows(2)->nullable()->columnSpanFull(),
            Textarea::make('notes')->rows(2)->nullable()->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->weight('bold'),

                TextColumn::make('maintenance_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FixedAssetMaintenanceRecord::TYPES[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'preventive'  => 'info',
                        'corrective'  => 'danger',
                        'inspection'  => 'primary',
                        'overhaul'    => 'warning',
                        default       => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => FixedAssetMaintenanceRecord::STATUSES[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        'scheduled'   => 'info',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),

                TextColumn::make('scheduled_date')->date('d M Y')->label('Scheduled')->placeholder('—'),
                TextColumn::make('completed_date')->date('d M Y')->label('Completed')->placeholder('—'),
                TextColumn::make('cost')->money('GHS')->placeholder('—'),
                TextColumn::make('contractor')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('next_due_date')->date('d M Y')->label('Next Due')->placeholder('—'),
            ])
            ->defaultSort('scheduled_date', 'desc')
            ->filters([
                SelectFilter::make('status')->options(FixedAssetMaintenanceRecord::STATUSES),
                SelectFilter::make('maintenance_type')->label('Type')->options(FixedAssetMaintenanceRecord::TYPES),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => array_merge($data, ['created_by_user_id' => Auth::id()])),
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