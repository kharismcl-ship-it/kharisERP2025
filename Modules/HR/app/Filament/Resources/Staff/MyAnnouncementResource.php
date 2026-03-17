<?php

namespace Modules\HR\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\HR\Models\Announcement;
use Modules\HR\Filament\Resources\Staff\MyAnnouncementResource\Schemas\AnnouncementInfolist;
use Modules\HR\Filament\Resources\Staff\MyAnnouncementResource\Tables\AnnouncementsTable;

class MyAnnouncementResource extends StaffSelfServiceResource
{
    protected static ?string $model = Announcement::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSpeakerWave;

    protected static ?string $navigationLabel = 'Announcements';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'announcements';

    public static function getEloquentQuery(): Builder
    {
        $companyId = Filament::getTenant()?->id;

        return parent::getEloquentQuery()
            ->where('company_id', $companyId)
            ->where('is_published', true)
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnnouncementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\HR\Filament\Resources\Staff\MyAnnouncementResource\Pages\ListAnnouncements::route('/'),
            'view'  => \Modules\HR\Filament\Resources\Staff\MyAnnouncementResource\Pages\ViewMyAnnouncement::route('/{record}'),
        ];
    }
}
