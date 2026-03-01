<?php
namespace Modules\Finance\Filament\Resources\FixedAssetResource\Pages;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Filament\Resources\FixedAssetResource;
class ViewFixedAsset extends ViewRecord {
    protected static string $resource = FixedAssetResource::class;
    protected function getHeaderActions(): array { return [EditAction::make()]; }
    public function infolist(Schema $schema): Schema {
        return $schema->components([
            Section::make('Details')->columns(2)->schema([
                TextEntry::make('name')->weight('bold'),
                TextEntry::make('company.name')->label('Company')->placeholder('System-wide'),
                TextEntry::make('created_at')->dateTime()->label('Created'),
                TextEntry::make('updated_at')->dateTime()->label('Updated'),
            ]),
        ]);
    }
}
