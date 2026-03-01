<?php

namespace Modules\Fleet\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
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
use Filament\Resources\Resource;
use Modules\Fleet\Filament\Resources\VehicleDocumentResource\Pages;
use Modules\Fleet\Models\VehicleDocument;

class VehicleDocumentResource extends Resource
{
    protected static ?string $model = VehicleDocument::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Fleet';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document Details')
                ->description('Vehicle document type, reference, and validity dates')
                ->columns(2)
                ->schema([
                    Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->relationship('vehicle', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('type')
                        ->label('Document Type')
                        ->options(array_combine(
                            VehicleDocument::TYPES,
                            array_map('ucfirst', VehicleDocument::TYPES)
                        ))
                        ->required(),

                    TextInput::make('document_number')
                        ->label('Document / Reference Number')
                        ->maxLength(100)
                        ->placeholder('e.g. GHA-2024-001234'),

                    DatePicker::make('issue_date')
                        ->label('Issue Date')
                        ->displayFormat('d M Y'),

                    DatePicker::make('expiry_date')
                        ->label('Expiry Date')
                        ->displayFormat('d M Y')
                        ->required(),
                ]),

            Section::make('Document File')
                ->collapsible()
                ->schema([
                    FileUpload::make('file_path')
                        ->label('Upload Document')
                        ->directory('fleet/documents')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(5120)
                        ->columnSpanFull(),
                    Textarea::make('notes')->rows(2)->columnSpanFull()->placeholder('Any additional notes...'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vehicle.name')->label('Vehicle')->searchable()->sortable(),
                TextColumn::make('vehicle.plate')->label('Plate'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('document_number')->label('Document No.')->placeholder('—'),
                TextColumn::make('issue_date')->date()->label('Issued'),
                TextColumn::make('expiry_date')
                    ->date()
                    ->label('Expires')
                    ->sortable()
                    ->color(fn ($record): ?string => $record?->is_expired
                        ? 'danger'
                        : ($record?->is_expiring_soon ? 'warning' : null)
                    )
                    ->weight(fn ($record): ?string => ($record?->is_expired || $record?->is_expiring_soon)
                        ? 'bold'
                        : null
                    ),
                TextColumn::make('expiry_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->is_expired
                        ? 'Expired'
                        : ($record->is_expiring_soon ? 'Expiring Soon' : 'Valid')
                    )
                    ->color(fn (string $state): string => match ($state) {
                        'Expired'       => 'danger',
                        'Expiring Soon' => 'warning',
                        'Valid'         => 'success',
                        default         => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(array_combine(
                        VehicleDocument::TYPES,
                        array_map('ucfirst', VehicleDocument::TYPES)
                    )),
                SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('expiry_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVehicleDocuments::route('/'),
            'create' => Pages\CreateVehicleDocument::route('/create'),
            'view'   => Pages\ViewVehicleDocument::route('/{record}'),
            'edit'   => Pages\EditVehicleDocument::route('/{record}/edit'),
        ];
    }
}
