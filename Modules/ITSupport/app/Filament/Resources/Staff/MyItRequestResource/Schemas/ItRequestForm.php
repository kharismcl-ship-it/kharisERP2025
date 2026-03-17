<?php

namespace Modules\ITSupport\Filament\Resources\Staff\MyItRequestResource\Schemas;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\ITSupport\Models\ItRequest;

class ItRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('What do you need help with?')
                ->columns(1)
                ->schema([
                    Forms\Components\TextInput::make('subject')
                        ->label('Subject')
                        ->required()
                        ->maxLength(150)
                        ->placeholder('Brief summary of the issue'),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->rows(4)
                        ->placeholder('Please describe the issue in detail — include any error messages, steps to reproduce, and what you were trying to do.'),
                ]),

            Section::make('Request Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('category')
                        ->options(ItRequest::CATEGORIES)
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('priority')
                        ->options(ItRequest::PRIORITIES)
                        ->default('medium')
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('department_id')
                        ->label('Your Department')
                        ->options(fn () => Department::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                        ->default(fn () => Employee::where('user_id', auth()->id())
                            ->where('company_id', Filament::getTenant()?->id)
                            ->value('department_id'))
                        ->searchable()
                        ->nullable()
                        ->helperText('Auto-filled from your HR profile — update if different'),
                ]),
        ]);
    }
}
