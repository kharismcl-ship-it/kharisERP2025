<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

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
use Filament\Facades\Filament;
use Modules\HR\Models\Shift;

class ShiftAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'shiftAssignments';

    protected static ?string $title = 'Shift Assignments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('shift_id')
                    ->label('Shift')
                    ->options(function () {
                        $companyId = app('current_company_id');
                        return Shift::where('company_id', $companyId)
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn ($s) => [$s->id => $s->name . ' (' . $s->start_time . ' – ' . $s->end_time . ')']);
                    })
                    ->required()->native(false),
                Forms\Components\DatePicker::make('effective_from')
                    ->required()->native(false),
                Forms\Components\DatePicker::make('effective_to')
                    ->nullable()->native(false),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('effective_from', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('shift.name')
                    ->label('Shift')->sortable(),
                Tables\Columns\TextColumn::make('shift.start_time')
                    ->label('Start')->time('g:i A'),
                Tables\Columns\TextColumn::make('shift.end_time')
                    ->label('End')->time('g:i A'),
                Tables\Columns\TextColumn::make('effective_from')->date()->sortable(),
                Tables\Columns\TextColumn::make('effective_to')->date()->placeholder('Ongoing'),
                Tables\Columns\TextColumn::make('notes')->limit(40)->placeholder('—'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['company_id'] = Filament::getTenant()?->id;
                        return $data;
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
