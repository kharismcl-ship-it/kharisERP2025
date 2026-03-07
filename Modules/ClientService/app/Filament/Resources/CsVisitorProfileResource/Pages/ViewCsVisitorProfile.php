<?php

namespace Modules\ClientService\Filament\Resources\CsVisitorProfileResource\Pages;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QROutputInterface;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\ClientService\Filament\Resources\CsVisitorProfileResource;
use Modules\ClientService\Models\CsVisitorProfile;

class ViewCsVisitorProfile extends ViewRecord
{
    protected static string $resource = CsVisitorProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile Information')
                ->schema([
                    TextEntry::make('full_name')->label('Full Name'),
                    TextEntry::make('phone')->label('Phone')->placeholder('—'),
                    TextEntry::make('email')->label('Email')->placeholder('—'),
                    TextEntry::make('id_type')
                        ->label('ID Type')
                        ->formatStateUsing(fn ($state) => \Modules\ClientService\Models\CsVisitor::ID_TYPES[$state] ?? $state)
                        ->placeholder('—'),
                    TextEntry::make('id_number')->label('ID Number')->placeholder('—'),
                    TextEntry::make('organization')->label('Organization')->placeholder('—'),
                    TextEntry::make('company.name')->label('Company'),
                ])
                ->columns(3),

            Section::make('Returning Visitor QR Code')
                ->description('The visitor saves this QR code on their phone to skip re-typing details on future visits.')
                ->schema([
                    TextEntry::make('profile_token')
                        ->label('QR Code')
                        ->html()
                        ->state(function (CsVisitorProfile $record): string {
                            $company = $record->company;
                            if (! $company) {
                                return '<p class="text-sm text-gray-400">Company not found — cannot generate URL.</p>';
                            }

                            $url     = route('clientservice.visitor-check-in.returning', [
                                'company'      => $company->slug,
                                'profileToken' => $record->profile_token,
                            ]);
                            $dataUri = (new QRCode(new QROptions([
                                'outputType' => QROutputInterface::GDIMAGE_PNG,
                                'scale'      => 6,
                            ])))->render($url);

                            return '<img src="' . e($dataUri) . '" alt="Profile QR Code" class="w-40 h-40 rounded-lg" />';
                        })
                        ->columnSpanFull(),

                    TextEntry::make('visits_count')
                        ->label('Total Visits')
                        ->state(fn (CsVisitorProfile $record) => $record->visits()->count()),
                ]),
        ]);
    }
}