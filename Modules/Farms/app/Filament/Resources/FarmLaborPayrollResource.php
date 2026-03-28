<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Resources\FarmLaborPayrollResource\Pages;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmLaborPayrollRecord;
use Modules\Farms\Models\FarmWorker;
use Filament\Facades\Filament;

class FarmLaborPayrollResource extends Resource
{
    protected static ?string $model = FarmLaborPayrollRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Farm Payroll';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Worker & Period')
                ->columns(2)
                ->schema([
                    Select::make('farm_id')
                        ->label('Farm')
                        ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->searchable()
                        ->live()
                        ->required(),

                    Select::make('farm_worker_id')
                        ->label('Farm Worker')
                        ->options(fn (Get $get) => FarmWorker::where('farm_id', $get('farm_id'))
                            ->where('is_active', true)
                            ->get()
                            ->mapWithKeys(fn ($w) => [$w->id => $w->display_name]))
                        ->searchable()
                        ->required(),

                    DatePicker::make('pay_period_start')->label('Period Start')->required(),
                    DatePicker::make('pay_period_end')->label('Period End')->required(),
                ]),

            Section::make('Pay Details')
                ->columns(2)
                ->schema([
                    Select::make('pay_type')
                        ->options([
                            'daily_rate'      => 'Daily Rate',
                            'piece_rate'      => 'Piece Rate',
                            'monthly_salary'  => 'Monthly Salary',
                            'weekly_rate'     => 'Weekly Rate',
                        ])
                        ->live()
                        ->required(),

                    TextInput::make('days_worked')
                        ->label('Days Worked')
                        ->numeric()
                        ->step(0.5)
                        ->visible(fn (Get $get): bool => in_array($get('pay_type'), ['daily_rate', 'weekly_rate'])),

                    TextInput::make('pieces_count')
                        ->label('Pieces / Units')
                        ->numeric()
                        ->step(0.01)
                        ->visible(fn (Get $get): bool => $get('pay_type') === 'piece_rate'),

                    TextInput::make('rate_per_day')->label('Rate per Day (GHS)')->numeric()->prefix('GHS')->step(0.01),
                    TextInput::make('rate_per_piece')->label('Rate per Piece (GHS)')->numeric()->prefix('GHS')->step(0.0001),
                    TextInput::make('monthly_salary')->label('Monthly Salary (GHS)')->numeric()->prefix('GHS')->step(0.01),
                    TextInput::make('gross_pay')->label('Gross Pay (GHS)')->numeric()->prefix('GHS')->required(),
                    TextInput::make('net_pay')->label('Net Pay (GHS)')->numeric()->prefix('GHS')->required(),
                ]),

            Section::make('Payment')
                ->columns(2)
                ->schema([
                    Select::make('payment_method')
                        ->options([
                            'cash'          => 'Cash',
                            'mobile_money'  => 'Mobile Money',
                            'bank_transfer' => 'Bank Transfer',
                        ])
                        ->default('cash')
                        ->live()
                        ->required(),

                    TextInput::make('momo_number')
                        ->label('MoMo Number')
                        ->tel()
                        ->maxLength(20)
                        ->visible(fn (Get $get): bool => $get('payment_method') === 'mobile_money'),

                    Select::make('status')
                        ->options([
                            'draft'    => 'Draft',
                            'approved' => 'Approved',
                            'paid'     => 'Paid',
                        ])
                        ->default('draft')
                        ->required(),

                    DatePicker::make('paid_date')
                        ->visible(fn (Get $get): bool => $get('status') === 'paid'),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')->rows(2)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_ref')->label('Ref')->searchable()->copyable(),
                TextColumn::make('farmWorker.name')->label('Worker')->sortable()->searchable(),
                TextColumn::make('pay_period_start')->date()->label('Period Start')->sortable(),
                TextColumn::make('pay_period_end')->date()->label('Period End')->toggleable(),
                TextColumn::make('pay_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('gross_pay')->money('GHS')->label('Gross')->sortable(),
                TextColumn::make('net_pay')->money('GHS')->label('Net')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'    => 'gray',
                        'approved' => 'info',
                        'paid'     => 'success',
                        default    => 'gray',
                    }),
                TextColumn::make('paid_date')->date()->placeholder('—')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('farm_id')->label('Farm')->relationship('farm', 'name'),
                SelectFilter::make('status')->options([
                    'draft'    => 'Draft',
                    'approved' => 'Approved',
                    'paid'     => 'Paid',
                ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (FarmLaborPayrollRecord $record): bool => $record->status === 'draft')
                    ->action(fn (FarmLaborPayrollRecord $record) => $record->update(['status' => 'approved'])),

                Action::make('mark_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (FarmLaborPayrollRecord $record): bool => $record->status === 'approved')
                    ->action(fn (FarmLaborPayrollRecord $record) => $record->update(['status' => 'paid', 'paid_date' => today()])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('pay_period_start', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFarmLaborPayrolls::route('/'),
            'create' => Pages\CreateFarmLaborPayroll::route('/create'),
            'edit'   => Pages\EditFarmLaborPayroll::route('/{record}/edit'),
        ];
    }
}
