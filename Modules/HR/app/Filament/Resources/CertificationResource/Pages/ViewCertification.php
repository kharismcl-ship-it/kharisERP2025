<?php

namespace Modules\HR\Filament\Resources\CertificationResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\HR\Filament\Resources\CertificationResource;
use Modules\HR\Models\Certification;

class ViewCertification extends ViewRecord
{
    protected static string $resource = CertificationResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Certification Details')->columns(2)->schema([
                TextEntry::make('employee_name')->label('Employee')
                    ->getStateUsing(fn ($record) => $record->employee->first_name . ' ' . $record->employee->last_name)
                    ->weight('bold'),
                TextEntry::make('name')->label('Certification Name'),
                TextEntry::make('issuing_authority')->label('Issued By')->placeholder('—'),
                TextEntry::make('certificate_number')->label('Certificate No.')->placeholder('—'),
                TextEntry::make('issue_date')->date()->label('Issue Date')->placeholder('—'),
                TextEntry::make('expiry_date')->date()->label('Expiry Date')->placeholder('No Expiry'),
                TextEntry::make('expiry_status')->label('Status')->badge()
                    ->getStateUsing(fn (Certification $record) => $record->is_expired
                        ? 'expired' : ($record->is_expiring_soon ? 'expiring_soon' : 'valid'))
                    ->color(fn (string $state): string => match ($state) {
                        'expired' => 'danger', 'expiring_soon' => 'warning', default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
            ]),
            Section::make('Notes')->collapsible()->schema([
                TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
            ]),
        ]);
    }
}
