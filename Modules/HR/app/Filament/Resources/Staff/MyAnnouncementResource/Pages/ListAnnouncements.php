<?php

namespace Modules\HR\Filament\Resources\Staff\MyAnnouncementResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\HR\Filament\Resources\Staff\MyAnnouncementResource;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = MyAnnouncementResource::class;
}
