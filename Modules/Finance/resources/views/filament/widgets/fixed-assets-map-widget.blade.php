@php
    $config     = $this->getMapData();
    $categories = $this->getCategoryOptions();
    $colorMap   = $this->buildCategoryColorMap();

    $categoryIcons = \Modules\Finance\Models\AssetCategory::orderBy('name')
        ->get(['name', 'map_icon']);
    $iconsByName = $categoryIcons->keyBy('name');
@endphp

<x-filament-widgets::widget>

    <x-filament::section>

        <x-slot name="heading">
            {{ $this->getHeading() }}
        </x-slot>

        {{-- Toolbar ---------------------------------------------------------------- --}}
        <div style="display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:space-between;
                    gap:1rem; margin-bottom:1rem; padding:0.75rem;
                    background:rgba(0,0,0,0.03); border-radius:0.5rem;
                    border:1px solid rgba(0,0,0,0.08);">

            {{-- Category filter: stacks label above select, stretches on small screens --}}
            <div style="display:flex; flex-direction:column; gap:0.25rem;
                        flex:1 1 180px; min-width:0; max-width:280px;">
                <label for="fa-cat-filter"
                       style="font-size:0.875rem; font-weight:500;">
                    Filter by category
                </label>
                <select id="fa-cat-filter"
                        wire:model.live="categoryFilter"
                        style="width:100%; font-size:0.875rem; border-radius:0.375rem;
                               border:1px solid #d1d5db; padding:0.375rem 0.625rem;
                               background:#fff; box-sizing:border-box;">
                    <option value="">All Categories</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" @selected($categoryFilter == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Legends (categories + status note) --------------------------------- --}}
            <div style="display:flex; flex-direction:column; gap:0.5rem;
                        flex:1 1 220px; min-width:0;">

                {{-- Category legend --}}
                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.5rem 1rem;
                            font-size:0.8125rem;">
                    <span style="font-weight:600; white-space:nowrap;">Categories:</span>
                    @foreach ($colorMap as $categoryName => $entry)
                        @php
                            $iconPath = $iconsByName[$categoryName]?->map_icon ?? null;
                            $iconUrl  = $iconPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($iconPath) : null;
                        @endphp
                        <span style="display:flex; align-items:center; gap:0.375rem; white-space:nowrap;">
                            @if ($iconUrl)
                                <img src="{{ $iconUrl }}" alt="{{ $categoryName }}"
                                     style="width:18px; height:18px; object-fit:contain; flex-shrink:0;" />
                            @else
                                <span style="display:inline-block; width:12px; height:12px;
                                             border-radius:50%; flex-shrink:0;
                                             background-color:{{ $entry['hex'] }};"></span>
                            @endif
                            {{ $categoryName }}
                        </span>
                    @endforeach
                </div>

                {{-- Status opacity note --}}
                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.375rem 1rem;
                            font-size:0.75rem; color:#6b7280;">
                    <span style="font-weight:600;">Status:</span>
                    <span style="display:flex; align-items:center; gap:0.375rem;">
                        <span style="display:inline-block; width:11px; height:11px; border-radius:50%;
                                     background:#9ca3af; opacity:1; flex-shrink:0;"></span>
                        Full = Active
                    </span>
                    <span style="display:flex; align-items:center; gap:0.375rem;">
                        <span style="display:inline-block; width:11px; height:11px; border-radius:50%;
                                     background:#9ca3af; opacity:0.55; flex-shrink:0;"></span>
                        Faded = Disposed
                    </span>
                    <span style="display:flex; align-items:center; gap:0.375rem;">
                        <span style="display:inline-block; width:11px; height:11px; border-radius:50%;
                                     background:#9ca3af; opacity:0.40; flex-shrink:0;"></span>
                        Very faded = Written Off
                    </span>
                </div>

            </div>
        </div>

        {{-- Leaflet map ------------------------------------------------------------- --}}
        <x-filament-leaflet::map :config="$config" widget />

    </x-filament::section>

    <x-filament-actions::modals />

</x-filament-widgets::widget>
