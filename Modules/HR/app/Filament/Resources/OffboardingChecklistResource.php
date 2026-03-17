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
use Modules\HR\Filament\Clusters\HrRelationsCluster;
use Modules\HR\Filament\Resources\OffboardingChecklistResource\Pages;
use Modules\HR\Models\OffboardingChecklist;

class OffboardingChecklistResource extends Resource
{
    protected static ?string $cluster = HrRelationsCluster::class;
    protected static ?string $model = OffboardingChecklist::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 67;

    protected static ?string $navigationLabel = 'Offboarding';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employee & Exit Details')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('employee_id')
                            ->label('Employee')
                            ->relationship('employee', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                            ->searchable()->preload()->required(),
                        Forms\Components\Select::make('resignation_type')
                            ->options(OffboardingChecklist::RESIGNATION_TYPES)
                            ->required()->native(false),
                        Forms\Components\Select::make('status')
                            ->options(OffboardingChecklist::STATUSES)
                            ->required()->default('initiated')->native(false),
                        Forms\Components\DatePicker::make('last_working_day')
                            ->native(false),
                        Forms\Components\Textarea::make('reason')
                            ->rows(3)->columnSpanFull(),
                    ]),

                Section::make('Checklist Items')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Toggle::make('assets_returned')
                            ->label('Assets Returned'),
                        Forms\Components\Toggle::make('access_revoked')
                            ->label('System Access Revoked'),
                        Forms\Components\Toggle::make('knowledge_transfer_done')
                            ->label('Knowledge Transfer Completed'),
                        Forms\Components\Toggle::make('clearance_signed')
                            ->label('Clearance Form Signed'),
                        Forms\Components\Toggle::make('final_payroll_processed')
                            ->label('Final Payroll Processed'),
                        Forms\Components\Toggle::make('exit_interview_done')
                            ->label('Exit Interview Conducted'),
                    ]),

                Section::make('Notes')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Textarea::make('exit_interview_notes')
                            ->label('Exit Interview Notes')
                            ->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('assets_notes')
                            ->label('Assets Notes')
                            ->rows(2)->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('resignation_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => OffboardingChecklist::RESIGNATION_TYPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('last_working_day')
                    ->date()->sortable(),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Progress')
                    ->suffix('%')
                    ->getStateUsing(fn ($record) => $record->completion_percentage),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => OffboardingChecklist::STATUSES[$state] ?? $state),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(OffboardingChecklist::STATUSES),
                Tables\Filters\SelectFilter::make('resignation_type')
                    ->options(OffboardingChecklist::RESIGNATION_TYPES),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffboardingChecklists::route('/'),
            'create' => Pages\CreateOffboardingChecklist::route('/create'),
            'view'   => Pages\ViewOffboardingChecklist::route('/{record}'),
            'edit'   => Pages\EditOffboardingChecklist::route('/{record}/edit'),
        ];
    }
}
