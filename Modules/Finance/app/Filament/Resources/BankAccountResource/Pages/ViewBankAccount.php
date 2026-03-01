<?php
namespace Modules\Finance\Filament\Resources\BankAccountResource\Pages;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\BankAccountResource;
class ViewBankAccount extends ViewRecord {
    protected static string $resource = BankAccountResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
    public function infolist(Schema $schema): Schema {
        return $schema->components([
            Section::make('Account Details')->columns(2)->schema([
                TextEntry::make('name')->weight('bold'),
                TextEntry::make('bank_name'),
                TextEntry::make('account_number'),
                TextEntry::make('branch')->placeholder('—'),
                TextEntry::make('currency')->badge()->color('gray'),
                TextEntry::make('opening_balance')->money('GHS'),
                TextEntry::make('glAccount.name')->label('GL Account')->placeholder('Not linked'),
                IconEntry::make('is_active')->boolean()->label('Active'),
                TextEntry::make('company.name')->label('Company'),
            ]),
            Section::make('Audit')->columns(2)->collapsible()->collapsed()->schema([
                TextEntry::make('created_at')->dateTime()->label('Created'),
                TextEntry::make('updated_at')->dateTime()->label('Updated'),
            ]),
        ]);
    }
}
