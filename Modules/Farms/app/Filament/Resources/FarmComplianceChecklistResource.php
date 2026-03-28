<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmComplianceChecklistResource\Pages;
use Modules\Farms\Models\FarmComplianceChecklist;

class FarmComplianceChecklistResource extends Resource
{
    protected static ?string $model = FarmComplianceChecklist::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Compliance Checklists';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Checklist Info')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_certification_id')
                        ->label('Linked Certification (optional)')
                        ->relationship('certification', 'certification_type')
                        ->searchable()
                        ->nullable(),

                    TextInput::make('checklist_name')->required()->maxLength(255),

                    Select::make('checklist_type')
                        ->options([
                            'globalGAP'   => 'GlobalGAP',
                            'organic'     => 'Organic',
                            'food_safety' => 'Food Safety',
                            'custom'      => 'Custom',
                        ])
                        ->required(),

                    TextInput::make('conducted_by')->maxLength(255),

                    DatePicker::make('audit_date'),
                    DatePicker::make('next_audit_date'),

                    Select::make('outcome')
                        ->options([
                            'pass'             => 'Pass',
                            'fail'             => 'Fail',
                            'conditional_pass' => 'Conditional Pass',
                            'pending'          => 'Pending',
                        ])
                        ->nullable(),
                ]),

            Section::make('Checklist Items')
                ->schema([
                    Repeater::make('items')
                        ->schema([
                            TextInput::make('question')->required()->columnSpanFull(),
                            Select::make('status')
                                ->options([
                                    'yes'     => 'Yes',
                                    'no'      => 'No',
                                    'na'      => 'N/A',
                                    'pending' => 'Pending',
                                ])
                                ->default('pending')
                                ->required(),
                            TextInput::make('evidence')->placeholder('Evidence reference'),
                            Textarea::make('notes')->rows(1),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add Item')
                        ->collapsible()
                        ->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('checklist_name')->searchable(),
                TextColumn::make('checklist_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'globalGAP'   => 'success',
                        'organic'     => 'info',
                        'food_safety' => 'warning',
                        default       => 'gray',
                    }),
                TextColumn::make('completion_pct')
                    ->label('Completion %')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1) . '%')
                    ->sortable(),
                TextColumn::make('audit_date')->date()->sortable()->placeholder('—'),
                TextColumn::make('outcome')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'pass'             => 'success',
                        'fail'             => 'danger',
                        'conditional_pass' => 'warning',
                        'pending'          => 'gray',
                        default            => 'gray',
                    })
                    ->placeholder('Pending'),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('checklist_type')->options([
                    'globalGAP'   => 'GlobalGAP',
                    'organic'     => 'Organic',
                    'food_safety' => 'Food Safety',
                    'custom'      => 'Custom',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('audit_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmComplianceChecklists::route('/'),
            'create' => Pages\CreateFarmComplianceChecklist::route('/create'),
            'edit'   => Pages\EditFarmComplianceChecklist::route('/{record}/edit'),
        ];
    }
}