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
use Modules\HR\Filament\Resources\SkillResource\Pages;
use Modules\HR\Models\Skill;
use Modules\HR\Models\SkillCategory;

class SkillResource extends Resource
{
    protected static ?string $cluster = HrLearningCluster::class;
    protected static ?string $model = Skill::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static ?int $navigationSort = 91;
    protected static ?string $navigationLabel = 'Skills Library';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Skill Details')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('skill_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()->preload()->nullable(),
                Forms\Components\Select::make('skill_type')
                    ->options([
                        'technical'     => 'Technical',
                        'soft'          => 'Soft Skill',
                        'leadership'    => 'Leadership',
                        'language'      => 'Language',
                        'certification' => 'Certification',
                    ])
                    ->required()->default('technical'),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable()->badge(),
                Tables\Columns\TextColumn::make('skill_type')->label('Type')
                    ->formatStateUsing(fn ($state) => str($state)->headline())
                    ->badge()->color('info'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('employeeSkills_count')->label('Employees')
                    ->counts('employeeSkills')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('skill_type')
                    ->options([
                        'technical' => 'Technical', 'soft' => 'Soft Skill',
                        'leadership' => 'Leadership', 'language' => 'Language',
                        'certification' => 'Certification',
                    ]),
            ])
            ->actions([EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit'   => Pages\EditSkill::route('/{record}/edit'),
        ];
    }
}