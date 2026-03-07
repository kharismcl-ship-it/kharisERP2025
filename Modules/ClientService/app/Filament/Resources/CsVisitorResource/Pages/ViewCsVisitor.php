<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;

use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
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

            Section::make('Visitor Pass')
                ->description('QR codes for this visit. The check-out QR closes the visit; the returning QR is permanent for this visitor.')
                ->schema([
                    TextEntry::make('badge_number')
                        ->label('Badge Code')
                        ->badge()
                        ->color('warning')
                        ->placeholder('None assigned'),

                    TextEntry::make('check_in_token')
                        ->label('Check-Out QR')
                        ->html()
                        ->state(function (CsVisitor $record): string {
                            if (! $record->check_in_token) {
                                return '<p class="text-sm text-gray-400">—</p>';
                            }
                            $company = $record->company;
                            if (! $company) {
                                return '<p class="text-sm text-gray-400">—</p>';
                            }
                            $dataUri = (new QRCode(new QROptions([
                                'outputType' => QROutputInterface::GDIMAGE_PNG,
                                'scale'      => 5,
                            ])))->render(route('clientservice.visitor-check-out', [
                                'company'      => $company->slug,
                                'checkInToken' => $record->check_in_token,
                            ]));
                            return '<img src="' . e($dataUri) . '" alt="Check-Out QR" class="w-32 h-32 rounded-lg" />';
                        }),

                    TextEntry::make('visitorProfile.profile_token')
                        ->label('Returning Visitor QR')
                        ->html()
                        ->state(function (CsVisitor $record): string {
                            $profile = $record->visitorProfile;
                            $company = $record->company;
                            if (! $profile || ! $company) {
                                return '<p class="text-sm text-gray-400">—</p>';
                            }
                            $dataUri = (new QRCode(new QROptions([
                                'outputType' => QROutputInterface::GDIMAGE_PNG,
                                'scale'      => 5,
                            ])))->render(route('clientservice.visitor-check-in.returning', [
                                'company'      => $company->slug,
                                'profileToken' => $profile->profile_token,
                            ]));
                            return '<img src="' . e($dataUri) . '" alt="Profile QR" class="w-32 h-32 rounded-lg" />';
                        }),
                ])
                ->columns(3),
        ]);
    }
}
