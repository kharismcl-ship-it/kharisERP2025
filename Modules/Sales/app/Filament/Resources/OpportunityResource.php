<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\OpportunityResource\Pages;
use Modules\Sales\Models\SalesOpportunity;

class OpportunityResource extends Resource
{
    protected static ?string $model = SalesOpportunity::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-chart-bar';
    protected static string|\UnitEnum|null   $navigationGroup = 'Pipeline';
    protected static ?int                    $navigationSort  = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Opportunity')->columns(2)->schema([
                TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Select::make('contact_id')
                    ->label('Contact')
                    ->relationship('contact', 'first_name')
                    ->searchable()->preload(),
                Select::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()->preload(),
                TextInput::make('estimated_value')->numeric()->prefix('GHS'),
                TextInput::make('probability_pct')->numeric()->suffix('%')->minValue(0)->maxValue(100)->default(50),
                Select::make('stage')
                    ->options(array_combine(SalesOpportunity::STAGES, array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), SalesOpportunity::STAGES))))
                    ->default('prospecting'),
                DatePicker::make('expected_close_date'),
                Select::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()->preload(),
                Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('contact.first_name')->label('Contact'),
                TextColumn::make('organization.name')->label('Organization'),
                TextColumn::make('estimated_value')->money('GHS')->sortable(),
                TextColumn::make('probability_pct')->suffix('%'),
                TextColumn::make('stage')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'closed_won'  => 'success',
                        'closed_lost' => 'danger',
                        'proposal'    => 'warning',
                        default       => 'info',
                    }),
                TextColumn::make('expected_close_date')->date(),
                TextColumn::make('assignedTo.name')->label('Owner'),
            ])
            ->filters([
                SelectFilter::make('stage')
                    ->options(array_combine(SalesOpportunity::STAGES, array_map('ucwords', array_map(fn ($s) => str_replace('_', ' ', $s), SalesOpportunity::STAGES)))),
            ])
            ->recordActions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'edit'   => Pages\EditOpportunity::route('/{record}/edit'),
        ];
    }
}