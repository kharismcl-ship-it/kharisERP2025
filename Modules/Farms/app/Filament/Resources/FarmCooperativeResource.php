<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmCooperativeResource\Pages;
use Modules\Farms\Filament\Resources\FarmCooperativeResource\RelationManagers\CooperativeMembersRelationManager;
use Modules\Farms\Models\FarmCooperative;

class FarmCooperativeResource extends Resource
{
    protected static ?string $model = FarmCooperative::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Cooperatives';

    protected static ?string $navigationLabel = 'Cooperatives / FBOs';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Cooperative Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),

                    TextInput::make('registration_number')
                        ->label('Registration Number')
                        ->maxLength(100),

                    Select::make('type')
                        ->options([
                            'cooperative'       => 'Cooperative',
                            'fbo'               => 'Farmer-Based Organisation (FBO)',
                            'outgrower_scheme'  => 'Outgrower Scheme',
                            'contract_farming'  => 'Contract Farming',
                        ])
                        ->default('cooperative')
                        ->required(),

                    Select::make('status')
                        ->options([
                            'active'    => 'Active',
                            'inactive'  => 'Inactive',
                            'suspended' => 'Suspended',
                        ])
                        ->default('active')
                        ->required(),
                ]),

            Section::make('Contact Information')
                ->columns(2)
                ->schema([
                    TextInput::make('contact_person')->maxLength(255),
                    TextInput::make('contact_phone')->tel()->maxLength(30),
                    TextInput::make('contact_email')->email()->maxLength(255),
                    Textarea::make('address')->rows(2)->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Cooperative Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('registration_number')->placeholder('—'),
                    TextEntry::make('type')->badge(),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'    => 'success',
                            'inactive'  => 'gray',
                            'suspended' => 'danger',
                            default     => 'gray',
                        }),
                    TextEntry::make('total_members')->label('Total Members'),
                    TextEntry::make('total_land_ha')->label('Total Land (ha)')->suffix(' ha'),
                ]),

            Section::make('Contact Information')
                ->columns(2)
                ->schema([
                    TextEntry::make('contact_person')->placeholder('—'),
                    TextEntry::make('contact_phone')->placeholder('—'),
                    TextEntry::make('contact_email')->placeholder('—'),
                    TextEntry::make('address')->placeholder('—')->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->schema([
                    TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cooperative'      => 'Cooperative',
                        'fbo'              => 'FBO',
                        'outgrower_scheme' => 'Outgrower',
                        'contract_farming' => 'Contract',
                        default            => $state,
                    }),
                TextColumn::make('total_members')->label('Members')->sortable(),
                TextColumn::make('total_land_ha')->label('Land (ha)')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'    => 'success',
                        'inactive'  => 'gray',
                        'suspended' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('type')->options([
                    'cooperative'      => 'Cooperative',
                    'fbo'              => 'FBO',
                    'outgrower_scheme' => 'Outgrower Scheme',
                    'contract_farming' => 'Contract Farming',
                ]),
                SelectFilter::make('status')->options(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended']),
            ])
            ->actions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelationManagers(): array
    {
        return [
            CooperativeMembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmCooperatives::route('/'),
            'create' => Pages\CreateFarmCooperative::route('/create'),
            'view'   => Pages\ViewFarmCooperative::route('/{record}'),
            'edit'   => Pages\EditFarmCooperative::route('/{record}/edit'),
        ];
    }
}