<?php

namespace Modules\HR\Filament\Forms\Components;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class LeaveAttachmentForm
{
    public static function make(): array
    {
        return [
            Repeater::make('attachments')
                ->label('Supporting Documents')
                ->schema([
                    FileUpload::make('file')
                        ->label('File')
                        ->required()
                        ->acceptedFileTypes([
                            'image/*',
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/plain',
                        ])
                        ->maxSize(10240) // 10MB
                        ->disk(config('filament.default_filesystem_disk'))
                        ->directory('leave-attachments')
                        ->visibility('private')
                        ->preserveFilenames()
                        ->storeFileNamesIn('original_name'),

                    Textarea::make('description')
                        ->label('Description')
                        ->placeholder('Brief description of this document')
                        ->maxLength(255),

                    Toggle::make('is_private')
                        ->label('Private Document')
                        ->helperText('Private documents are only visible to HR and managers')
                        ->default(false),
                ])
                ->columns(1)
                ->itemLabel(fn (array $state): ?string => $state['original_name'] ?? null)
                ->maxItems(5)
                ->collapsible()
                ->cloneable()
                ->grid(2),
        ];
    }
}
