<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmCertificationResource\Pages;
use Modules\Farms\Models\FarmCertification;

class FarmCertificationResource extends Resource
{
    protected static ?string $model = FarmCertification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Certifications';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Certification Details')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('certification_type')
                        ->label('Certification Type')
                        ->options([
                            'GlobalGAP'          => 'GlobalGAP',
                            'Organic'            => 'Organic',
                            'Fairtrade'          => 'Fairtrade',
                            'Rainforest Alliance' => 'Rainforest Alliance',
                            'BRCGS'              => 'BRCGS',
                            'ISO 22000'          => 'ISO 22000',
                            'Ghana FDA'          => 'Ghana FDA',
                            'Other'              => 'Other',
                        ])
                        ->searchable()
                        ->required(),

                    TextInput::make('certifying_body')
                        ->label('Certifying Body')
                        ->placeholder('e.g. Control Union, Bureau Veritas')
                        ->maxLength(255),

                    TextInput::make('certificate_number')
                        ->label('Certificate Number')
                        ->maxLength(255),
                ]),

            Section::make('Dates & Status')
                ->columns(3)
                ->schema([
                    DatePicker::make('issued_date')->label('Issued Date'),
                    DatePicker::make('expiry_date')->label('Expiry Date'),

                    Select::make('status')
                        ->options([
                            'active'          => 'Active',
                            'expired'         => 'Expired',
                            'suspended'       => 'Suspended',
                            'pending_renewal' => 'Pending Renewal',
                            'under_audit'     => 'Under Audit',
                        ])
                        ->default('pending_renewal')
                        ->required(),

                    TextInput::make('renewal_reminder_days')
                        ->label('Reminder Days Before Expiry')
                        ->numeric()
                        ->default(60)
                        ->suffix('days'),
                ]),

            Section::make('Scope & Notes')
                ->schema([
                    Textarea::make('scope')
                        ->label('Scope (crops/products covered)')
                        ->rows(2)
                        ->columnSpanFull(),

                    Textarea::make('notes')
                        ->rows(2)
                        ->columnSpanFull(),

                    FileUpload::make('document_path')
                        ->label('Certificate Document')
                        ->directory('farm-certifications')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Certification Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('farm.name')->label('Farm'),
                    TextEntry::make('certification_type')->label('Type'),
                    TextEntry::make('certifying_body')->placeholder('—'),
                    TextEntry::make('certificate_number')->placeholder('—'),
                ]),

            Section::make('Dates & Status')
                ->columns(3)
                ->schema([
                    TextEntry::make('issued_date')->date()->placeholder('—'),
                    TextEntry::make('expiry_date')->date()->placeholder('—'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active'          => 'success',
                            'expired'         => 'danger',
                            'suspended'       => 'warning',
                            'pending_renewal' => 'gray',
                            'under_audit'     => 'info',
                            default           => 'gray',
                        }),
                    TextEntry::make('renewal_reminder_days')->label('Reminder Days')->suffix(' days'),
                ]),

            Section::make('Scope & Notes')
                ->schema([
                    TextEntry::make('scope')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm')->sortable()->searchable(),
                TextColumn::make('certification_type')->label('Type')->searchable(),
                TextColumn::make('certificate_number')->label('Cert #')->placeholder('—')->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active'          => 'success',
                        'expired'         => 'danger',
                        'suspended'       => 'warning',
                        'pending_renewal' => 'gray',
                        'under_audit'     => 'info',
                        default           => 'gray',
                    }),
                TextColumn::make('issued_date')->date()->sortable()->toggleable(),
                TextColumn::make('expiry_date')->date()->sortable(),
                TextColumn::make('days_until_expiry')
                    ->label('Days Until Expiry')
                    ->state(fn (FarmCertification $record): string => $record->daysUntilExpiry() !== null
                        ? (string) $record->daysUntilExpiry()
                        : '—')
                    ->color(fn (FarmCertification $record): string => ($record->daysUntilExpiry() !== null && $record->daysUntilExpiry() < 30)
                        ? 'danger'
                        : 'gray')
                    ->sortable(false),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'active'          => 'Active',
                    'expired'         => 'Expired',
                    'suspended'       => 'Suspended',
                    'pending_renewal' => 'Pending Renewal',
                    'under_audit'     => 'Under Audit',
                ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('renew')
                    ->label('Renew')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->form([
                        DatePicker::make('issued_date')->required(),
                        DatePicker::make('expiry_date')->required(),
                    ])
                    ->action(function (FarmCertification $record, array $data): void {
                        $record->update([
                            'issued_date' => $data['issued_date'],
                            'expiry_date' => $data['expiry_date'],
                            'status'      => 'active',
                        ]);
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('expiry_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmCertifications::route('/'),
            'create' => Pages\CreateFarmCertification::route('/create'),
            'view'   => Pages\ViewFarmCertification::route('/{record}'),
            'edit'   => Pages\EditFarmCertification::route('/{record}/edit'),
        ];
    }
}
