<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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
use Modules\HR\Filament\Resources\EmployeeBenefitResource\Pages;
use Modules\HR\Models\EmployeeBenefit;

class EmployeeBenefitResource extends Resource
{
    protected static ?string $model = EmployeeBenefit::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 25;

    protected static ?string $navigationLabel = 'Employee Benefits';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Benefit Assignment')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('benefit_type_id')
                            ->label('Benefit Type')
                            ->relationship('benefitType', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('status')
                            ->options(EmployeeBenefit::STATUSES)
                            ->required()->default('active')->native(false),
                        Forms\Components\DatePicker::make('start_date')
                            ->required()->native(false),
                        Forms\Components\DatePicker::make('end_date')
                            ->nullable()->native(false),
                    ]),

                Section::make('Contribution Overrides')
                    ->collapsible()
                    ->description('Leave blank to use the default rates from the benefit type.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('employer_contribution_override')
                            ->label('Employer Contribution Override')
                            ->numeric()->prefix('GHS')->nullable(),
                        Forms\Components\TextInput::make('employee_contribution_override')
                            ->label('Employee Contribution Override')
                            ->numeric()->prefix('GHS')->nullable(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('benefitType.name')
                    ->label('Benefit')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->placeholder('Ongoing'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'pending'  => 'warning',
                        'inactive' => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => EmployeeBenefit::STATUSES[$state] ?? ucfirst($state)),
                Tables\Columns\TextColumn::make('employer_contribution_override')
                    ->label('Employer Override')->money('GHS')->placeholder('Default'),
                Tables\Columns\TextColumn::make('employee_contribution_override')
                    ->label('Employee Override')->money('GHS')->placeholder('Default'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(EmployeeBenefit::STATUSES),
                Tables\Filters\SelectFilter::make('benefit_type')
                    ->relationship('benefitType', 'name'),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListEmployeeBenefits::route('/'),
            'create' => Pages\CreateEmployeeBenefit::route('/create'),
            'view'   => Pages\ViewEmployeeBenefit::route('/{record}'),
            'edit'   => Pages\EditEmployeeBenefit::route('/{record}/edit'),
        ];
    }
}
