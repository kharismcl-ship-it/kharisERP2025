<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Farm Map</h1>
                <p class="text-sm text-gray-500">{{ $farm->name }}</p>
            </div>
            <a href="{{ route('farms.dashboard', $farm->slug) }}"
               class="text-sm text-indigo-600 hover:underline">Back to Dashboard</a>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden" x-data="farmMap(@js($this->plotsGeoJson), @js($farm->latitude), @js($farm->longitude))">

            <!-- Legend -->
            <div class="p-4 border-b flex gap-6 text-sm">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span> Active
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-3 h-3 rounded-sm bg-yellow-400"></span> Fallow
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-3 h-3 rounded-sm bg-blue-400"></span> Preparing
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-3 h-3 rounded-sm bg-gray-400"></span> Other
                </span>
            </div>

            <!-- Map container -->
            <div id="farm-map" style="height: 520px;" x-ref="mapEl"></div>

            <!-- Plot detail panel -->
            <div x-show="selectedPlot" x-transition class="p-4 border-t bg-gray-50">
                <template x-if="selectedPlot">
                    <div class="flex items-center gap-6 text-sm">
                        <div>
                            <span class="text-gray-500">Plot:</span>
                            <span class="ml-1 font-medium" x-text="selectedPlot.name"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Status:</span>
                            <span class="ml-1 capitalize" x-text="selectedPlot.status"></span>
                        </div>
                        <div x-show="selectedPlot.area">
                            <span class="text-gray-500">Area:</span>
                            <span class="ml-1" x-text="selectedPlot.area + ' ha'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        @if($farm->plots->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-400 text-sm">
                No plots with geometry data are available for this farm.
            </div>
        @endif

    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
function farmMap(geoJsonData, lat, lng) {
    return {
        map: null,
        selectedPlot: null,

        init() {
            const centerLat = lat || 0;
            const centerLng = lng || 0;

            this.map = L.map(this.$refs.mapEl).setView([centerLat, centerLng], lat ? 14 : 2);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);

            let data;
            try {
                data = typeof geoJsonData === 'string' ? JSON.parse(geoJsonData) : geoJsonData;
            } catch (e) {
                return;
            }

            if (!data || !data.features || data.features.length === 0) return;

            const self = this;

            const layer = L.geoJSON(data, {
                style: function(feature) {
                    return self.plotStyle(feature.properties.status);
                },
                onEachFeature: function(feature, layer) {
                    const props = feature.properties;
                    layer.bindTooltip(props.name || 'Plot', { sticky: true });
                    layer.on('click', function() {
                        self.selectedPlot = props;
                    });
                }
            }).addTo(this.map);

            this.map.fitBounds(layer.getBounds(), { padding: [20, 20] });
        },

        plotStyle(status) {
            const colors = {
                active:     '#22c55e',
                fallow:     '#facc15',
                preparing:  '#60a5fa',
            };
            const color = colors[status] || '#9ca3af';
            return {
                color: color,
                weight: 2,
                opacity: 0.9,
                fillColor: color,
                fillOpacity: 0.35,
            };
        }
    };
}
</script>
@endpush
