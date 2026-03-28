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
use Modules\HR\Filament\Clusters\HrSafetyCluster;
use Modules\HR\Filament\Resources\SafetyInspectionResource\Pages;
use Modules\HR\Models\SafetyInspection;

class SafetyInspectionResource extends Resource
{
    protected static ?string $cluster = HrSafetyCluster::class;
    protected static ?string $model = SafetyInspection::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;
    protected static ?int $navigationSort = 72;
    protected static ?string $navigationLabel = 'Inspections';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inspection Details')->columns(2)->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('location')->required()->maxLength(255),
                Forms\Components\DatePicker::make('inspection_date')->required()->default(now()),
                Forms\Components\Select::make('inspected_by_employee_id')
                    ->label('Inspector')
                    ->relationship('inspectedBy', 'full_name')
                    ->searchable()->preload()->nullable(),
                Forms\Components\Select::make('status')
                    ->options(['scheduled' => 'Scheduled', 'in_progress' => 'In Progress', 'completed' => 'Completed'])
                    ->required()->default('scheduled'),
                Forms\Components\Toggle::make('overall_passed')->label('Overall Passed')->inline(false),
                Forms\Components\Toggle::make('follow_up_required')->inline(false)->live(),
                Forms\Components\DatePicker::make('follow_up_date')
                    ->visible(fn ($get) => $get('follow_up_required')),
                Forms\Components\Textarea::make('summary_notes')->rows(4)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('inspection_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('location')->searchable(),
                Tables\Columns\TextColumn::make('inspection_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('inspectedBy.full_name')->label('Inspector')->placeholder('—'),
                Tables\Columns\IconColumn::make('overall_passed')->boolean()->label('Passed'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'scheduled' => 'info', 'in_progress' => 'warning', 'completed' => 'success', default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('follow_up_required')->boolean()->label('Follow-up'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['scheduled' => 'Scheduled', 'in_progress' => 'In Progress', 'completed' => 'Completed']),
            ])
            ->actions([EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSafetyInspections::route('/'),
            'create' => Pages\CreateSafetyInspection::route('/create'),
            'edit'   => Pages\EditSafetyInspection::route('/{record}/edit'),
        ];
    }
}