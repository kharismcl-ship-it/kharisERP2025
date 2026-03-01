<?php

namespace Modules\ManufacturingPaper\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ManufacturingPaper\Filament\Resources\MpPaperGradeResource\Pages;
use Modules\ManufacturingPaper\Models\MpPaperGrade;

class MpPaperGradeResource extends Resource
{
    protected static ?string $model = MpPaperGrade::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Manufacturing Paper';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    Select::make('category')
                        ->options(array_combine(MpPaperGrade::CATEGORIES, array_map('ucwords', array_map(fn ($c) => str_replace('_', ' ', $c), MpPaperGrade::CATEGORIES))))
                        ->required(),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('gsm')->label('GSM (g/m²)')->numeric()->step(0.01),
                    TextInput::make('width_mm')->label('Width (mm)')->numeric()->step(0.01),
                    TextInput::make('color')->maxLength(50)->default('white'),
                ]),
                Toggle::make('is_active')->default(true)->inline(false),
                Textarea::make('description')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'printing'  => 'primary',
                        'packaging' => 'warning',
                        'tissue'    => 'info',
                        'specialty' => 'success',
                        default     => 'gray',
                    }),
                TextColumn::make('gsm')->label('GSM')->numeric(decimalPlaces: 2),
                TextColumn::make('width_mm')->label('Width (mm)')->numeric(decimalPlaces: 2),
                TextColumn::make('color'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(array_combine(MpPaperGrade::CATEGORIES, array_map('ucwords', array_map(fn ($c) => str_replace('_', ' ', $c), MpPaperGrade::CATEGORIES)))),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMpPaperGrades::route('/'),
            'create' => Pages\CreateMpPaperGrade::route('/create'),
            'edit'   => Pages\EditMpPaperGrade::route('/{record}/edit'),
        ];
    }
}
