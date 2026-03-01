<?php

namespace Modules\Construction\Filament\Resources;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
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
use Modules\Construction\Filament\Resources\ContractorResource\Pages;
use Modules\Construction\Filament\Resources\ContractorResource\RelationManagers;
use Modules\Construction\Models\Contractor;

class ContractorResource extends Resource
{
    protected static ?string $model = Contractor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    Select::make('specialization')
                        ->options([
                            'civil'         => 'Civil',
                            'structural'    => 'Structural',
                            'architectural' => 'Architectural',
                            'electrical'    => 'Electrical',
                            'plumbing'      => 'Plumbing',
                            'mechanical'    => 'Mechanical',
                            'interior'      => 'Interior',
                            'landscaping'   => 'Landscaping',
                            'general'       => 'General',
                        ])
                        ->searchable(),
                ]),
                Grid::make(3)->schema([
                    TextInput::make('contact_person')->label('Contact Person')->maxLength(255),
                    TextInput::make('phone')->maxLength(50),
                    TextInput::make('email')->email()->maxLength(255),
                ]),
                Textarea::make('address')->rows(2)->columnSpanFull(),
                Grid::make(3)->schema([
                    TextInput::make('license_number')->label('License No.')->maxLength(100),
                    DatePicker::make('license_expiry')->label('License Expiry'),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('specialization')
                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : '—'),
                TextColumn::make('contact_person')->label('Contact'),
                TextColumn::make('phone'),
                TextColumn::make('email'),
                TextColumn::make('tasks_count')
                    ->counts('tasks')
                    ->label('Tasks'),
                TextColumn::make('license_expiry')
                    ->label('License Expiry')
                    ->date()
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : null),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\SelectFilter::make('specialization')
                    ->options([
                        'civil'         => 'Civil',
                        'structural'    => 'Structural',
                        'architectural' => 'Architectural',
                        'electrical'    => 'Electrical',
                        'plumbing'      => 'Plumbing',
                        'mechanical'    => 'Mechanical',
                        'interior'      => 'Interior',
                        'landscaping'   => 'Landscaping',
                        'general'       => 'General',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProjectTasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContractors::route('/'),
            'create' => Pages\CreateContractor::route('/create'),
            'view'   => Pages\ViewContractor::route('/{record}'),
            'edit'   => Pages\EditContractor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'contact_person', 'email'];
    }
}
