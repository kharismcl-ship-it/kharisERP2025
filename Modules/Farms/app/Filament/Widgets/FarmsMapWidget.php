<?php

namespace Modules\Farms\Filament\Widgets;

use EduardoRibeiroDev\FilamentLeaflet\Support\Markers\Marker;
use EduardoRibeiroDev\FilamentLeaflet\Widgets\MapWidget;
use Modules\Farms\Models\Farm;

class FarmsMapWidget extends MapWidget
{
    protected string $view = 'filament-leaflet::widgets.map-widget';

    protected ?string $heading = 'Farm Locations';

    protected int|string|array $columnSpan = 'full';

    protected function getMarkers(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $farms = Farm::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return $farms->map(function (Farm $farm) {
            $marker = Marker::make((float) $farm->latitude, (float) $farm->longitude)
                ->title($farm->name)
                ->popupContent(
                    "<b>{$farm->name}</b><br>{$farm->location}<br>Status: " . ucfirst($farm->status ?? 'unknown')
                );

            return $marker;
        })->toArray();
    }

    protected function getMapCenter(): array
    {
        $companyId = auth()->user()?->current_company_id;

        $farm = Farm::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();

        if ($farm) {
            return [(float) $farm->latitude, (float) $farm->longitude];
        }

        // Default to Ghana centre
        return [7.9465, -1.0232];
    }

    protected function getDefaultZoom(): int
    {
        return 7;
    }

    protected function getMapHeight(): int
    {
        return 400;
    }
}
