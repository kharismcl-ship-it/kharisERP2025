<?php

namespace Modules\Finance\Filament\Widgets;

use EduardoRibeiroDev\FilamentLeaflet\Support\Markers\Marker;
use EduardoRibeiroDev\FilamentLeaflet\Widgets\MapWidget;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Storage;
use Modules\Finance\Models\AssetCategory;
use Modules\Finance\Models\FixedAsset;

class FixedAssetsMapWidget extends MapWidget
{
    protected string $view = 'finance::filament.widgets.fixed-assets-map-widget';

    protected ?string $heading = 'Fixed Assets Map';

    /** Ghana geographical centre */
    protected array $mapCenter = [7.9465, -1.0232];

    protected int $defaultZoom = 7;

    protected int $mapHeight = 640;

    protected bool $hasFullscreenControl = true;

    protected bool $hasDrawMarkerControl = true;

    protected bool $hasDrawCircleMarkerControl = true;

    protected bool $hasRemoveLayersControl = true;

    protected bool $hasScaleControl = true;

    protected bool $hasSearchControl = true;

    protected bool $hasZoomControl = true;

    /** Selected category ID, empty string = show all */
    public string $categoryFilter = '';

    /**
     * Colour palette cycled through categories (Filament Color + matching hex for the legend).
     * Order is stable as long as categories are fetched ordered by name.
     */
    private const PALETTE = [
        ['color' => Color::Blue,    'hex' => '#3b82f6'],
        ['color' => Color::Orange,  'hex' => '#f97316'],
        ['color' => Color::Violet,  'hex' => '#8b5cf6'],
        ['color' => Color::Amber,   'hex' => '#f59e0b'],
        ['color' => Color::Teal,    'hex' => '#14b8a6'],
        ['color' => Color::Pink,    'hex' => '#ec4899'],
        ['color' => Color::Indigo,  'hex' => '#6366f1'],
        ['color' => Color::Lime,    'hex' => '#84cc16'],
        ['color' => Color::Sky,     'hex' => '#0ea5e9'],
        ['color' => Color::Rose,    'hex' => '#f43f5e'],
    ];

    /**
     * Returns ['Category Name' => ['color' => Filament Color array, 'hex' => '#rrggbb']]
     * Colours are assigned by alphabetical order of category name so they stay consistent
     * regardless of DB insertion order.
     */
    public function buildCategoryColorMap(): array
    {
        $categories = AssetCategory::orderBy('name')->pluck('name')->values();
        $map = [];

        foreach ($categories as $i => $name) {
            $map[$name] = self::PALETTE[$i % count(self::PALETTE)];
        }

        return $map;
    }

    public function getCategoryOptions(): array
    {
        return AssetCategory::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function updatedCategoryFilter(): void
    {
        $this->refreshMap();
    }

    protected function getMarkers(): array
    {
        $colorMap = $this->buildCategoryColorMap();

        $query = FixedAsset::query()
            ->with(['category', 'custodian'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0);

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        return $query->get()->map(function (FixedAsset $asset) use ($colorMap) {
            $categoryName = $asset->category?->name ?? 'Uncategorised';
            $color        = $colorMap[$categoryName]['color'] ?? Color::Gray;

            // Reduce opacity for non-active assets so status is visually apparent
            $opacity = match ($asset->status) {
                'disposed'    => 0.55,
                'written_off' => 0.40,
                default       => 1.0,
            };

            $status = ucfirst(str_replace('_', ' ', $asset->status));
            $nbv    = number_format($asset->netBookValue(), 2);

            // Asset photo (shown at top of popup if uploaded)
            $photoHtml = '';
            if ($asset->photo) {
                $photoUrl  = Storage::disk('public')->url($asset->photo);
                $photoHtml = '<img src="' . $photoUrl . '" alt="' . e($asset->name) . '" '
                           . 'style="width:100%;max-width:100%;height:auto;max-height:130px;'
                           . 'object-fit:cover;border-radius:6px;display:block;margin-bottom:8px;" />';
            }

            $rows = '<tr><td colspan="2" style="font-weight:700;font-size:0.9rem;padding-bottom:4px;">'
                  . e($asset->asset_code) . ' — ' . e($asset->name)
                  . '</td></tr>'
                  . '<tr><td style="color:#6b7280;padding-right:8px;white-space:nowrap;">Category</td>'
                  . '<td>' . e($categoryName) . '</td></tr>'
                  . '<tr><td style="color:#6b7280;padding-right:8px;white-space:nowrap;">Status</td>'
                  . '<td>' . $status . '</td></tr>'
                  . '<tr><td style="color:#6b7280;padding-right:8px;white-space:nowrap;">NBV</td>'
                  . '<td>GHS ' . $nbv . '</td></tr>'
                  . ($asset->location
                      ? '<tr><td style="color:#6b7280;padding-right:8px;white-space:nowrap;">Location</td>'
                        . '<td>' . e($asset->location) . '</td></tr>'
                      : '')
                  . ($asset->custodian
                      ? '<tr><td style="color:#6b7280;padding-right:8px;white-space:nowrap;">Custodian</td>'
                        . '<td>' . e($asset->custodian->full_name) . '</td></tr>'
                      : '');

            $popup = '<div style="min-width:200px;max-width:min(280px,calc(100vw - 80px));'
                   . 'font-size:0.8125rem;line-height:1.5;box-sizing:border-box;">'
                   . $photoHtml
                   . '<table style="width:100%;border-collapse:collapse;">' . $rows . '</table>'
                   . '</div>';

            // Use the category's uploaded map icon; fall back to color marker
            $iconPath = $asset->category?->map_icon;
            $iconUrl  = $iconPath ? Storage::disk('public')->url($iconPath) : null;

            $marker = Marker::make((float) $asset->latitude, (float) $asset->longitude)
                ->record($asset, false)
                ->title($asset->asset_code . ' — ' . $asset->name . ' (' . $status . ')')
                ->popupContent($popup)
                ->opacity($opacity)
                ->group($categoryName);

            if ($iconUrl) {
                $marker->icon($iconUrl, [30, 30]);
            } else {
                $marker->color($color);
            }

            return $marker;
        })->all();
    }
}
