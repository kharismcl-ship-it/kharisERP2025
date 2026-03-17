<?php

namespace Modules\HR\Filament\Resources\Staff\MyCertificationResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Certification Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Certification Name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('issuing_authority')
                        ->label('Issuing Authority / Body')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('certificate_number')
                        ->label('Certificate Number / ID')
                        ->maxLength(100)
                        ->nullable(),
                    Forms\Components\DatePicker::make('issue_date')
                        ->required(),
                    Forms\Components\DatePicker::make('expiry_date')
                        ->nullable()
                        ->helperText('Leave blank if certification does not expire'),
                    Forms\Components\FileUpload::make('certificate_path')
                        ->label('Upload Certificate')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(5120)
                        ->directory('hr/certifications')
                        ->nullable(),
                    Forms\Components\Textarea::make('notes')
                        ->rows(2)
                        ->nullable(),
                ]),
        ]);
    }
}
