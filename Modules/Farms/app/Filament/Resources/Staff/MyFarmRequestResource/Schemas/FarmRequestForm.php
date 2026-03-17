<?php

namespace Modules\Farms\Filament\Resources\Staff\MyFarmRequestResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Farms\Models\FarmRequest;

class FarmRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Farm Request')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('request_type')
                        ->label('Request Type')
                        ->options(array_combine(
                            FarmRequest::REQUEST_TYPES,
                            array_map(fn ($t) => ucfirst($t), FarmRequest::REQUEST_TYPES)
                        ))
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('urgency')
                        ->options(array_combine(
                            FarmRequest::URGENCIES,
                            array_map('ucfirst', FarmRequest::URGENCIES)
                        ))
                        ->required()
                        ->native(false),

                    Forms\Components\Textarea::make('description')
                        ->label('Description / Details')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
