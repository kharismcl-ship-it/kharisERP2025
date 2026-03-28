<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource\Pages;
use Modules\Requisition\Models\RequisitionReminderRule;

class RequisitionReminderRuleResource extends Resource
{
    protected static ?string $model = RequisitionReminderRule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Reminder Rules';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rule Configuration')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    Select::make('trigger_status')
                        ->label('Trigger When Status Is')
                        ->options([
                            'submitted'        => 'Submitted',
                            'under_review'     => 'Under Review',
                            'pending_revision' => 'Pending Revision',
                        ])
                        ->required(),
                ]),
                TextInput::make('hours_after_trigger')
                    ->label('Hours of Inactivity Before Reminder')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                CheckboxList::make('reminder_channels')
                    ->label('Send Via')
                    ->options([
                        'email'    => 'Email',
                        'sms'      => 'SMS',
                        'whatsapp' => 'WhatsApp',
                    ])
                    ->default(['email'])
                    ->columns(3),
                Grid::make(3)->schema([
                    Toggle::make('notify_requester')->label('Notify Requester')->default(true)->inline(false),
                    Toggle::make('notify_approvers')->label('Notify Approvers')->default(true)->inline(false),
                    Toggle::make('escalate_urgency')->label('Escalate Urgency')->default(false)->inline(false),
                ]),
                Toggle::make('is_active')->label('Active')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('trigger_status')
                    ->label('Trigger Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => str_replace('_', ' ', ucfirst($state))),
                TextColumn::make('hours_after_trigger')->label('Hours'),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionReminderRules::route('/'),
            'create' => Pages\CreateRequisitionReminderRule::route('/create'),
            'edit'   => Pages\EditRequisitionReminderRule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query  = parent::getEloquentQuery();
        $tenant = filament()->getTenant();

        if ($tenant) {
            $query->where('company_id', $tenant->getKey());
        }

        return $query;
    }
}