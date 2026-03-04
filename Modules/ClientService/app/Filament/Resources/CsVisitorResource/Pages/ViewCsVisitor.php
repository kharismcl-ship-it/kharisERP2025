<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ClientService\Filament\Resources\CsVisitorResource;
use Modules\ClientService\Models\CsVisitor;
use Modules\CommunicationCentre\Concerns\HasCommunicationActions;

class ViewCsVisitor extends ViewRecord
{
    use HasCommunicationActions;

    protected static string $resource = CsVisitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            ...$this->communicationActions(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Visitor Information')
                ->schema([
                    TextEntry::make('company.name')->label('Company'),
                    TextEntry::make('full_name')->label('Full Name'),
                    TextEntry::make('phone')->label('Phone')->placeholder('—'),
                    TextEntry::make('email')->label('Email')->placeholder('—'),
                    TextEntry::make('id_type')
                        ->label('ID Type')
                        ->formatStateUsing(fn ($state) => CsVisitor::ID_TYPES[$state] ?? $state)
                        ->placeholder('—'),
                    TextEntry::make('id_number')->label('ID Number')->placeholder('—'),
                    TextEntry::make('organization')->label('Organization')->placeholder('—'),
                ])
                ->columns(3),

            Section::make('Visit Details')
                ->schema([
                    TextEntry::make('purpose_of_visit')
                        ->label('Purpose of Visit')
                        ->columnSpanFull(),
                    TextEntry::make('hostEmployee.full_name')
                        ->label('Host Employee')
                        ->placeholder('—'),
                    TextEntry::make('department.name')
                        ->label('Department')
                        ->placeholder('—'),
                    TextEntry::make('badge_number')->label('Badge Number')->placeholder('—'),
                    TextEntry::make('items_brought')->label('Items Brought')->placeholder('—'),
                    TextEntry::make('notes')->label('Notes')->placeholder('—')->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Check In / Check Out')
                ->schema([
                    TextEntry::make('check_in_at')->label('Checked In')->dateTime(),
                    TextEntry::make('check_out_at')
                        ->label('Checked Out')
                        ->dateTime()
                        ->placeholder('Still In'),
                    TextEntry::make('duration')
                        ->label('Duration')
                        ->state(fn (CsVisitor $record) => $record->duration ?? '—'),
                    TextEntry::make('is_checked_out')
                        ->label('Status')
                        ->badge()
                        ->state(fn (CsVisitor $record) => $record->is_checked_out ? 'Out' : 'In')
                        ->color(fn ($state) => $state === 'Out' ? 'success' : 'warning'),
                    TextEntry::make('checkedInBy.name')
                        ->label('Checked In By')
                        ->placeholder('—'),
                    TextEntry::make('checkedOutBy.name')
                        ->label('Checked Out By')
                        ->placeholder('—'),
                ])
                ->columns(4),

            Section::make('Photo')
                ->schema([
                    ImageEntry::make('photo_path')
                        ->label('')
                        ->height(200)
                        ->columnSpanFull(),
                ])
                ->visible(fn (CsVisitor $record) => (bool) $record->photo_path),
        ]);
    }
}
