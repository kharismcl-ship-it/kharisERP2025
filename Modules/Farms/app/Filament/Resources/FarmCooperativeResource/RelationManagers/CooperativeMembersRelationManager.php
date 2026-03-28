<?php

namespace Modules\Farms\Filament\Resources\FarmCooperativeResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CooperativeMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Member Farms';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('farm_id')
                ->label('Farm')
                ->relationship('farm', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('member_number')->label('Member Number')->maxLength(100),
            DatePicker::make('membership_date'),
            TextInput::make('land_area_ha')->label('Land Area (ha)')->numeric()->step(0.0001)->suffix('ha'),
            TextInput::make('share_count')->label('Share Count')->numeric()->nullable(),
            Toggle::make('is_active')->default(true)->inline(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm'),
                TextColumn::make('member_number')->label('Member #')->placeholder('—'),
                TextColumn::make('membership_date')->date()->placeholder('—'),
                TextColumn::make('land_area_ha')->label('Land (ha)')->suffix(' ha')->placeholder('—'),
                TextColumn::make('share_count')->label('Shares')->placeholder('—'),
                IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
