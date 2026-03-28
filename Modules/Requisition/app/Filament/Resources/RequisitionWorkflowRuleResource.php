<?php

declare(strict_types=1);

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Modules\HR\Models\Employee;
use Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource\Pages;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionWorkflowRule;

class RequisitionWorkflowRuleResource extends Resource
{
    protected static ?string $model = RequisitionWorkflowRule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?string $navigationLabel = 'Workflow Rules';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rule Trigger')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->columnSpanFull(),

                Select::make('request_type')
                    ->label('Request Type (leave blank for all types)')
                    ->options(Requisition::TYPES)
                    ->nullable()
                    ->placeholder('— All Types —'),

                TextInput::make('min_amount')
                    ->label('Minimum Amount (GHS)')
                    ->numeric()
                    ->nullable()
                    ->prefix('GHS')
                    ->helperText('Rule applies when total_estimated_cost >= this value.'),

                TextInput::make('max_amount')
                    ->label('Maximum Amount (GHS, exclusive)')
                    ->numeric()
                    ->nullable()
                    ->prefix('GHS')
                    ->helperText('Rule applies when total_estimated_cost < this value. Leave blank for no upper limit.'),

                Select::make('cost_centre_id')
                    ->label('Cost Centre (leave blank for all)')
                    ->relationship('costCentre', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->integer()
                    ->default(0)
                    ->helperText('Lower numbers are evaluated first.'),
            ])->columns(2),

            Section::make('Approvers to Add')->schema([
                Repeater::make('approvers')
                    ->label('Approver Entries')
                    ->schema([
                        Select::make('employee_id')
                            ->label('Employee')
                            ->options(fn () => Employee::query()
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id')
                                ->toArray()
                            )
                            ->searchable()
                            ->required(),

                        Select::make('role')
                            ->label('Role')
                            ->options([
                                'reviewer' => 'Reviewer',
                                'approver' => 'Approver',
                            ])
                            ->default('reviewer')
                            ->required(),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->addActionLabel('Add Approver')
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if (! $record) {
                            $component->state([]);
                            return;
                        }
                        $ids   = $record->approver_employee_ids ?? [];
                        $roles = $record->approver_roles ?? [];
                        $rows  = [];
                        foreach ($ids as $i => $id) {
                            $rows[] = [
                                'employee_id' => $id,
                                'role'        => $roles[$i] ?? 'reviewer',
                            ];
                        }
                        $component->state($rows);
                    })
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('request_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? (Requisition::TYPES[$state] ?? $state) : 'All Types')
                    ->color(fn ($state) => match ($state) {
                        'fund'      => 'warning',
                        'material'  => 'info',
                        'equipment' => 'success',
                        'service'   => 'gray',
                        null        => 'primary',
                        default     => 'primary',
                    }),
                TextColumn::make('min_amount')
                    ->label('Min (GHS)')
                    ->money('GHS')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('max_amount')
                    ->label('Max (GHS)')
                    ->money('GHS')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->using(function (RequisitionWorkflowRule $record, array $data): RequisitionWorkflowRule {
                        // Extract approver_employee_ids and approver_roles from the virtual 'approvers' repeater
                        // The repeater is dehydrated(false) so we read it from the raw request
                        $approvers = request()->input('data.approvers', []);
                        if (is_array($approvers)) {
                            $data['approver_employee_ids'] = array_values(array_column($approvers, 'employee_id'));
                            $data['approver_roles']        = array_values(array_column($approvers, 'role'));
                        }
                        unset($data['approvers']);

                        $record->update($data);
                        return $record;
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionWorkflowRules::route('/'),
            'create' => Pages\CreateRequisitionWorkflowRule::route('/create'),
            'edit'   => Pages\EditRequisitionWorkflowRule::route('/{record}/edit'),
        ];
    }
}