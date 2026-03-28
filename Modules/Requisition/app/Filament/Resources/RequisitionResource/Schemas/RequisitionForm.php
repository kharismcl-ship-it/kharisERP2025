<?php

namespace Modules\Requisition\Filament\Resources\RequisitionResource\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Requisition\Models\Requisition;

class RequisitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')->schema([
                Grid::make(2)->schema([
                    Select::make('template_id')
                        ->label('Load from Template')
                        ->relationship('template', 'name', fn ($query) => $query->where('is_active', true))
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('Select a template to pre-fill type, urgency, cost centre, and title.')
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if (! $state) return;
                            $template = \Modules\Requisition\Models\RequisitionTemplate::find($state);
                            if (! $template) return;
                            $set('request_type', $template->request_type);
                            $set('urgency', $template->urgency);
                            $set('cost_centre_id', $template->cost_centre_id);
                            if ($template->default_title) {
                                $set('title', $template->default_title);
                            }
                        })
                        ->columnSpanFull(),
                ]),
                Grid::make(2)->schema([
                    Select::make('company_id')
                        ->label('Requesting Company')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('Company raising this request.')
                        ->columnSpanFull(),
                ]),
                Grid::make(2)->schema([
                    Select::make('requester_employee_id')
                        ->label('Requester')
                        ->relationship('requesterEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('target_company_id')
                        ->label('Target Company')
                        ->relationship('targetCompany', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText('Leave blank for internal request.'),
                ]),
                Grid::make(2)->schema([
                    Select::make('target_department_id')
                        ->label('Target Department')
                        ->relationship('targetDepartment', 'name')
                        ->searchable()
                        ->preload(),
                    Select::make('request_type')
                        ->options(Requisition::TYPES)
                        ->required()
                        ->default('general')
                        ->live(),
                ]),
                Grid::make(2)->schema([
                    Select::make('urgency')
                        ->options(Requisition::URGENCIES)
                        ->required()
                        ->default('medium'),
                    DatePicker::make('due_by')
                        ->label('Due By')
                        ->nullable()
                        ->helperText('Optional SLA / deadline date.'),
                ]),
                TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Textarea::make('description')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Budget & Procurement')->schema([
                Grid::make(2)->schema([
                    Select::make('cost_centre_id')
                        ->label('Cost Centre')
                        ->relationship('costCentre', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    TextInput::make('total_estimated_cost')
                        ->label('Total Estimated Cost')
                        ->numeric()
                        ->prefix('GHS')
                        ->nullable()
                        ->helperText('Auto-updated from items when unit costs are provided. Editable for general/fund requests.'),
                ]),
                Select::make('preferred_vendor_id')
                    ->label('Preferred Vendor')
                    ->relationship('preferredVendor', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('For material/equipment requests — used to auto-create a Purchase Order on approval.')
                    ->visible(fn ($get) => in_array($get('request_type'), Requisition::COSTED_TYPES))
                    ->columnSpanFull(),
            ]),

            Section::make('Status & Resolution')->schema([
                Grid::make(2)->schema([
                    Select::make('status')
                        ->options(Requisition::STATUSES)
                        ->default('draft')
                        ->required(),
                    Select::make('approved_by')
                        ->label('Approved By')
                        ->relationship('approvedByUser', 'name')
                        ->searchable()
                        ->nullable(),
                ]),
                Textarea::make('rejection_reason')
                    ->label('Rejection Reason / Revision Notes')
                    ->rows(2)
                    ->columnSpanFull()
                    ->visible(fn ($get) => in_array($get('status'), ['rejected', 'pending_revision'])),
                Textarea::make('cancellation_reason')
                    ->label('Cancellation Reason')
                    ->rows(2)
                    ->columnSpanFull()
                    ->visible(fn ($get) => $get('status') === 'cancelled'),
                Grid::make(2)->schema([
                    DateTimePicker::make('approved_at')->label('Approved At')->nullable(),
                    DateTimePicker::make('fulfilled_at')->label('Fulfilled At')->nullable(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),

            Section::make('Notification Preferences')->schema([
                CheckboxList::make('notification_channels')
                    ->label('Notify parties via')
                    ->options(Requisition::NOTIFICATION_CHANNELS)
                    ->default(['email', 'database'])
                    ->columns(4)
                    ->helperText('Choose which channels are used when notifying reviewers, approvers, and parties about this request.'),
            ]),
        ]);
    }
}