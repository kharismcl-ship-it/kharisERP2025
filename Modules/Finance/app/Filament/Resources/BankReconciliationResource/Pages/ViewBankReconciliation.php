<?php
namespace Modules\Finance\Filament\Resources\BankReconciliationResource\Pages;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\BankReconciliationResource;
use Modules\Finance\Models\BankReconciliation;
class ViewBankReconciliation extends ViewRecord {
    protected static string $resource = BankReconciliationResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
    public function infolist(Schema $schema): Schema {
        return $schema->components([
            Section::make('Reconciliation Details')->columns(2)->schema([
                TextEntry::make('bankAccount.name')->label('Bank Account')->weight('bold'),
                TextEntry::make('statement_date')->date(),
                TextEntry::make('statement_balance')->money('GHS'),
                TextEntry::make('book_balance')->money('GHS'),
                TextEntry::make('difference')->money('GHS'),
                TextEntry::make('status')->badge()
                    ->formatStateUsing(fn (string $state) => BankReconciliation::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => $state === 'reconciled' ? 'success' : 'warning'),
                TextEntry::make('reconciledBy.name')->label('Reconciled By')->placeholder('Pending'),
                TextEntry::make('reconciled_at')->dateTime()->placeholder('—'),
                TextEntry::make('notes')->columnSpanFull()->placeholder('None'),
            ]),
            Section::make('Audit')->columns(2)->collapsible()->collapsed()->schema([
                TextEntry::make('created_at')->dateTime()->label('Created'),
                TextEntry::make('updated_at')->dateTime()->label('Updated'),
            ]),
        ]);
    }
}
