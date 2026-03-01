<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Filament\Resources\LeaveApprovalWorkflowResource\Pages;
use Modules\HR\Models\LeaveApprovalWorkflow;

class LeaveApprovalWorkflowResource extends Resource
{
    protected static ?string $model = LeaveApprovalWorkflow::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Workflow Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                        Forms\Components\Toggle::make('requires_all_approvals')
                            ->label('Require All Approvals')
                            ->helperText('If enabled, all approval levels must approve. If disabled, any approval can approve.')
                            ->default(false),
                        Forms\Components\TextInput::make('timeout_days')
                            ->label('Timeout (Days)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(30)
                            ->default(3)
                            ->helperText('Days before auto-approval if no action taken'),
                    ])
                    ->columns(2),

                Section::make('Approval Levels')
                    ->schema([
                        Forms\Components\Repeater::make('levels')
                            ->relationship('levels')
                            ->schema([
                                Forms\Components\TextInput::make('level_number')
                                    ->label('Level #')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\Select::make('approver_type')
                                    ->label('Approver Type')
                                    ->options([
                                        'manager' => 'Direct Manager',
                                        'department_head' => 'Department Head',
                                        'specific_employee' => 'Specific Employee',
                                        'hr' => 'HR Representative',
                                    ])
                                    ->required()
                                    ->reactive(),
                                Forms\Components\Select::make('approver_employee_id')
                                    ->label('Specific Employee')
                                    ->options(fn () => \Modules\HR\Models\Employee::all()->pluck('full_name', 'id'))
                                    ->visible(fn (Get $get) => $get('approver_type') === 'specific_employee'),
                                Forms\Components\Select::make('approver_department_id')
                                    ->label('Department')
                                    ->options(fn () => \Modules\HR\Models\Department::all()->pluck('name', 'id'))
                                    ->visible(fn (Get $get) => $get('approver_type') === 'department_head'),
                                Forms\Components\TextInput::make('approver_role')
                                    ->label('Role Pattern')
                                    ->placeholder('e.g., HR Manager, Department Head')
                                    ->visible(fn (Get $get) => $get('approver_type') === 'hr'),
                                Forms\Components\Toggle::make('is_required')
                                    ->label('Required')
                                    ->default(true)
                                    ->helperText('Is this approval level required?'),
                                Forms\Components\TextInput::make('approval_order')
                                    ->label('Approval Order')
                                    ->numeric()
                                    ->required()
                                    ->default(1),
                            ])
                            ->defaultItems(1)
                            ->columnSpanFull()
                            ->orderable('approval_order'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('levels_count')
                    ->label('Levels')
                    ->counts('levels'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('requires_all_approvals')
                    ->label('All Required')
                    ->boolean(),
                Tables\Columns\TextColumn::make('timeout_days')
                    ->label('Timeout (Days)'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->query(fn (Builder $query) => $query->where('is_active', true)),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\LevelsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveApprovalWorkflows::route('/'),
            'create' => Pages\CreateLeaveApprovalWorkflow::route('/create'),
            'edit' => Pages\EditLeaveApprovalWorkflow::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('levels');
    }
}
