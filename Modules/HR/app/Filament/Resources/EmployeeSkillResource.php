<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Clusters\HrLearningCluster;
use Modules\HR\Filament\Resources\EmployeeSkillResource\Pages;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeSkill;
use Modules\HR\Models\Skill;

class EmployeeSkillResource extends Resource
{
    protected static ?string $cluster = HrLearningCluster::class;
    protected static ?string $model = EmployeeSkill::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?int $navigationSort = 92;
    protected static ?string $navigationLabel = 'Skills Inventory';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Employee Skill')->columns(2)->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'full_name')
                    ->searchable()->preload()->required(),
                Forms\Components\Select::make('skill_id')
                    ->label('Skill')
                    ->relationship('skill', 'name')
                    ->searchable()->preload()->required(),
                Forms\Components\Select::make('proficiency_level')
                    ->options([
                        1 => '1 – Beginner',
                        2 => '2 – Basic',
                        3 => '3 – Intermediate',
                        4 => '4 – Advanced',
                        5 => '5 – Expert',
                    ])
                    ->required()->default(1),
                Forms\Components\DatePicker::make('acquired_date'),
                Forms\Components\DatePicker::make('expiry_date'),
                Forms\Components\Select::make('verified_by_employee_id')
                    ->label('Verified By')
                    ->relationship('verifiedBy', 'full_name')
                    ->searchable()->preload()->nullable(),
                Forms\Components\DatePicker::make('verified_at'),
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')->label('Employee')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('skill.name')->label('Skill')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('skill.category.name')->label('Category')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('proficiency_level')->label('Proficiency')
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        1 => 'Beginner', 2 => 'Basic', 3 => 'Intermediate',
                        4 => 'Advanced', 5 => 'Expert', default => $state,
                    })
                    ->badge()->color(fn ($state) => match ((int) $state) {
                        1, 2 => 'warning', 3 => 'info', 4 => 'success', 5 => 'primary', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('expiry_date')->date()->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('verifiedBy.full_name')->label('Verified By')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('skill_id')
                    ->label('Skill')
                    ->relationship('skill', 'name')
                    ->searchable()->preload(),
            ])
            ->actions([EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployeeSkills::route('/'),
            'create' => Pages\CreateEmployeeSkill::route('/create'),
            'edit'   => Pages\EditEmployeeSkill::route('/{record}/edit'),
        ];
    }
}