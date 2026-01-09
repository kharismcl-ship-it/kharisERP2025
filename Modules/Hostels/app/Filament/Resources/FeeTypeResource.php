<?php

namespace Modules\Hostels\Filament\Resources;

    use BackedEnum;
    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\DeleteBulkAction;
    use Filament\Actions\EditAction;
    use Filament\Forms\Components\Select;
    use Filament\Forms\Components\TextInput;
    use Filament\Infolists\Components\TextEntry;
    use Filament\Resources\Resource;
    use Filament\Schemas\Schema;
    use Filament\Support\Icons\Heroicon;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Table;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Modules\Hostels\Filament\Resources\FeeTypeResource\Pages;
    use Modules\Hostels\Models\FeeType;

    class FeeTypeResource extends Resource {
        protected static ?string $model = FeeType::class;

        protected static ?string $slug = 'fee-types';

        protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

        PUBLIC static function form(Schema $schema): Schema
        {
        return $schema
        ->components([//
        Select::make('company_id')
        ->relationship('company', 'name')
        ->searchable()
        ->required(),

        TextInput::make('name')
        ->required(),

        TextInput::make('amount')
        ->required()
        ->numeric(),

        TextEntry::make('created_at')
        ->label('Created Date')
        ->state(fn (?FeeType $record): string => $record?->created_at?->diffForHumans() ?? '-'),

        TextEntry::make('updated_at')
        ->label('Last Modified Date')
        ->state(fn (?FeeType $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
        ]);
        }

        PUBLIC static function table(Table $table): Table
        {
        return $table
        ->columns([
        TextColumn::make('company.name')
        ->searchable()
        ->sortable(),

        TextColumn::make('name')
        ->searchable()
        ->sortable(),

        TextColumn::make('amount'),
        ])
        ->filters([
        //
        ])
        ->recordActions([
        EditAction::make(),
        DeleteAction::make(),
        ])
        ->toolbarActions([
        BulkActionGroup::make([
        DeleteBulkAction::make(),
        ]),
        ]);
        }

        public static function getPages(): array
        {
        return [
        'index' => Pages\ListFeeTypes::route('/'),
'create' => Pages\CreateFeeType::route('/create'),
'edit' => Pages\EditFeeType::route('/{record}/edit'),
        ];
        }

        PUBLIC static function getGlobalSearchEloquentQuery(): Builder
        {
        return parent::getGlobalSearchEloquentQuery()->with(['company']);
        }

        PUBLIC static function getGloballySearchableAttributes(): array
        {
        return ['name', 'company.name'];
        }

        PUBLIC static function getGlobalSearchResultDetails(Model $record): array
        {
        $details = [];

        if ($record->company) {
$details['Company'] = $record->company->name;}

        return $details;
        }
    }
