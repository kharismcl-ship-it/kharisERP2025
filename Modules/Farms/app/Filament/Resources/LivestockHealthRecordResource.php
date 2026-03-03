<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\LivestockCluster;
use Filament\Resources\Resource;
use Modules\Farms\Filament\Resources\LivestockHealthRecordResource\Pages;
use Modules\Farms\Models\LivestockHealthRecord;

class LivestockHealthRecordResource extends Resource
{
    protected static ?string $model = LivestockHealthRecord::class;

    protected static ?string $cluster = LivestockCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Livestock Health';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event Details')
                ->columns(2)
                ->schema([
                    Select::make('livestock_batch_id')
                        ->label('Livestock Batch')
                        ->relationship('livestockBatch', 'batch_reference')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('event_type')
                        ->options(array_combine(
                            LivestockHealthRecord::EVENT_TYPES,
                            array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), LivestockHealthRecord::EVENT_TYPES))
                        ))
                        ->required(),

                    DatePicker::make('event_date')->required(),

                    TextInput::make('administered_by')->maxLength(255),

                    Textarea::make('description')->required()->rows(3)->columnSpanFull(),
                ]),

            Section::make('Treatment Details')
                ->columns(3)
                ->schema([
                    TextInput::make('medicine_used')->maxLength(255)->placeholder('—'),
                    TextInput::make('dosage')->maxLength(255)->placeholder('—'),
                    TextInput::make('cost')->numeric()->prefix('GHS')->step(0.01),
                    DatePicker::make('next_due_date')->label('Next Due Date'),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),

            Section::make('Attachments')
                ->collapsible()
                ->collapsed()
                ->schema([
                    FileUpload::make('attachments')
                        ->multiple()
                        ->image()
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->maxFiles(5)
                        ->directory('farm-livestock-health')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('livestockBatch.batch_reference')
                    ->label('Batch')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('event_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('primary'),

                TextColumn::make('event_date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(40),

                TextColumn::make('medicine_used')
                    ->label('Medicine')
                    ->placeholder('—'),

                TextColumn::make('cost')
                    ->money('GHS'),

                TextColumn::make('next_due_date')
                    ->date('d M Y')
                    ->label('Next Due')
                    ->placeholder('—')
                    ->color(fn ($state) => $state && now()->gte($state) ? 'danger' : null),

                TextColumn::make('administered_by')
                    ->label('By')
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('event_type')
                    ->options(array_combine(
                        LivestockHealthRecord::EVENT_TYPES,
                        array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), LivestockHealthRecord::EVENT_TYPES))
                    )),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('event_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLivestockHealthRecords::route('/'),
            'create' => Pages\CreateLivestockHealthRecord::route('/create'),
            'view'   => Pages\ViewLivestockHealthRecord::route('/{record}'),
            'edit'   => Pages\EditLivestockHealthRecord::route('/{record}/edit'),
        ];
    }
}
