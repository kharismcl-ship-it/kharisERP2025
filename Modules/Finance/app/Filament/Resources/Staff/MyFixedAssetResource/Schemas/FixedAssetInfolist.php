<?php

namespace Modules\Finance\Filament\Resources\Staff\MyFixedAssetResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Finance\Models\FixedAsset;

class FixedAssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Asset Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('asset_code')->label('Asset Code')->badge()->color('gray'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'active'      => 'success',
                            'disposed'    => 'gray',
                            'written_off' => 'danger',
                            default       => 'gray',
                        })
                        ->formatStateUsing(fn ($state) => FixedAsset::STATUSES[$state] ?? ucfirst($state)),
                    TextEntry::make('category.name')->label('Category')->placeholder('—'),
                    TextEntry::make('name')->columnSpanFull()->weight('bold'),
                    TextEntry::make('serial_number')->label('Serial Number')->placeholder('—'),
                    TextEntry::make('location')->placeholder('—'),
                    TextEntry::make('cost')->money('KES'),
                    TextEntry::make('acquisition_date')->date()->label('Acquired')->placeholder('—'),
                    TextEntry::make('warranty_expiry_date')->date()->label('Warranty Expires')->placeholder('—'),
                    TextEntry::make('description')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }
}
