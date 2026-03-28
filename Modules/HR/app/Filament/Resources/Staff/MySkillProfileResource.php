<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSkill;
use Modules\HR\Models\Skill;

class MySkillProfileResource extends StaffSelfServiceResource
{
    protected static ?string $model = EmployeeSkill::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static ?string $navigationLabel = 'My Skills';
    protected static string|\UnitEnum|null $navigationGroup = 'HR';
    protected static ?int $navigationSort = 60;
    protected static ?string $slug = 'my-skills';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;
        $employee  = Employee::where('user_id', auth()->id())
            ->where('company_id', $companyId)
            ->first();

        if (! $employee) {
            return parent::getEloquentQuery()->whereRaw('1=0');
        }

        return parent::getEloquentQuery()->where('employee_id', $employee->id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('skill.name')
            ->columns([
                Tables\Columns\TextColumn::make('skill.name')->label('Skill')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('skill.category.name')->label('Category')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('proficiency_level')->label('Proficiency')
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        1 => 'Beginner', 2 => 'Basic', 3 => 'Intermediate',
                        4 => 'Advanced', 5 => 'Expert', default => $state,
                    })
                    ->badge()->color(fn ($state) => match ((int) $state) {
                        1, 2 => 'warning', 3 => 'info', 4 => 'success', 5 => 'primary', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('acquired_date')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('expiry_date')->date()->placeholder('—')
                    ->color(fn ($record) => $record->expiry_date && $record->expiry_date->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('verifiedBy.full_name')->label('Verified By')->placeholder('—'),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\HR\Filament\Resources\Staff\MySkillProfileResource\Pages\ListMySkills::route('/'),
        ];
    }
}