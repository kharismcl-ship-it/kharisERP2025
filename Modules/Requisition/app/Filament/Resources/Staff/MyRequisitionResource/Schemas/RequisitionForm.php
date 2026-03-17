<?php

namespace Modules\Requisition\Filament\Resources\Staff\MyRequisitionResource\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\ProcurementInventory\Models\Vendor;
use Modules\Requisition\Models\Requisition;

class RequisitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Requisition Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Request Title')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull()
                        ->placeholder('e.g. Office supplies for Q2, Laptop replacement for John'),
                    Forms\Components\Select::make('request_type')
                        ->label('Type')
                        ->options(Requisition::TYPES)
                        ->required()
                        ->native(false)
                        ->live(),
                    Forms\Components\Select::make('urgency')
                        ->options(Requisition::URGENCIES)
                        ->default('medium')
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('target_department_id')
                        ->label('Department')
                        ->options(fn () => Department::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->default(fn () => Employee::where('user_id', auth()->id())
                            ->where('company_id', Filament::getTenant()?->id)
                            ->value('department_id'))
                        ->searchable()
                        ->nullable()
                        ->helperText('Auto-filled from your HR profile — update if different'),
                    Forms\Components\DatePicker::make('due_by')
                        ->label('Required By')
                        ->native(false)
                        ->nullable(),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Describe what you need and why, including quantities or specifications.'),
                ]),

            Section::make('Cost & Vendor')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('total_estimated_cost')
                        ->label('Estimated Cost')
                        ->numeric()
                        ->prefix('GHS')
                        ->nullable(),
                    Forms\Components\Select::make('preferred_vendor_id')
                        ->label('Preferred Vendor')
                        ->options(fn () => Vendor::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->hidden(fn (Get $get) => ! in_array($get('request_type'), Requisition::COSTED_TYPES))
                        ->helperText('Optional — suggest a vendor for material or equipment requests'),
                ]),
        ]);
    }
}
