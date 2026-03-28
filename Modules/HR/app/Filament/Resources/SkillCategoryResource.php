<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Clusters\HrSetupCluster;
use Modules\HR\Filament\Resources\SkillCategoryResource\Pages;
use Modules\HR\Models\SkillCategory;

class SkillCategoryResource extends Resource
{
    protected static ?string $cluster = HrSetupCluster::class;
    protected static ?string $model = SkillCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static ?int $navigationSort = 90;
    protected static ?string $navigationLabel = 'Skill Categories';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Skill Category')->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\ColorPicker::make('color')->default('#6366f1'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label(''),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('skills_count')->label('Skills')
                    ->counts('skills')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSkillCategories::route('/'),
            'create' => Pages\CreateSkillCategory::route('/create'),
            'edit'   => Pages\EditSkillCategory::route('/{record}/edit'),
        ];
    }
}