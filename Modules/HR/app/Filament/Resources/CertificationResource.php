<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\CertificationResource\Pages;
use Modules\HR\Models\Certification;

class CertificationResource extends Resource
{
    protected static ?string $model = Certification::class;

    /**
     * This model has no direct company_id — Filament's ownership
     * check is skipped. Data isolation is handled via the parent
     * relationship or a custom getEloquentQuery() scope.
     */
    protected static bool $isScopedToTenant = false;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'Learning & Development';

    protected static ?int $navigationSort = 59;

    protected static ?string $navigationLabel = 'Certifications';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Certification Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\TextInput::make('name')->required()->maxLength(200),
                        Forms\Components\TextInput::make('issuing_authority')->maxLength(150)->nullable(),
                        Forms\Components\TextInput::make('certificate_number')->maxLength(100)->nullable(),
                        Forms\Components\DatePicker::make('issue_date')->native(false)->nullable(),
                        Forms\Components\DatePicker::make('expiry_date')->native(false)->nullable(),
                        Forms\Components\FileUpload::make('certificate_path')
                            ->label('Certificate File')
                            ->disk('public')->directory('hr/certifications')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Certification')
                    ->searchable()
                    ->description(fn ($record) => $record->issuing_authority),
                Tables\Columns\TextColumn::make('certificate_number')->label('Cert. No.')->placeholder('—'),
                Tables\Columns\TextColumn::make('issue_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')->date()->sortable()->placeholder('No expiry'),
                Tables\Columns\TextColumn::make('expiry_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (Certification $r) => $r->is_expired ? 'expired' : ($r->is_expiring_soon ? 'expiring_soon' : 'valid'))
                    ->color(fn (string $state): string => match ($state) {
                        'expired'       => 'danger',
                        'expiring_soon' => 'warning',
                        'valid'         => 'success',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'expired' => 'Expired', 'expiring_soon' => 'Expiring Soon', default => 'Valid',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($r) => $r->first_name . ' ' . $r->last_name),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCertifications::route('/'),
            'create' => Pages\CreateCertification::route('/create'),
            'view'   => Pages\ViewCertification::route('/{record}'),
            'edit'   => Pages\EditCertification::route('/{record}/edit'),
        ];
    }
}
