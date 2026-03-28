<?php

namespace Modules\Farms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmWeatherLog;
use Modules\Farms\Models\FarmWeatherAlert;

class FetchWeatherCommand extends Command
{
    protected $signature = 'farms:fetch-weather';

    protected $description = 'Fetch current weather for all active farms via Open-Meteo API and generate alerts';

    public function handle(): void
    {
        $farms = Farm::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'active')
            ->get();

        foreach ($farms as $farm) {
            try {
                $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'      => $farm->latitude,
                    'longitude'     => $farm->longitude,
                    'current'       => 'temperature_2m,relative_humidity_2m,wind_speed_10m,precipitation,weather_code',
                    'forecast_days' => 1,
                    'timezone'      => 'Africa/Accra',
                ]);

                if (! $response->ok()) {
                    continue;
                }

                $data     = $response->json('current');
                $temp     = $data['temperature_2m'] ?? null;
                $humidity = $data['relative_humidity_2m'] ?? null;
                $wind     = $data['wind_speed_10m'] ?? null;
                $rain     = $data['precipitation'] ?? null;

                // Store weather log if model/table exists
                if (class_exists(FarmWeatherLog::class)) {
                    FarmWeatherLog::create([
                        'company_id' => $farm->company_id,
                        'farm_id'    => $farm->id,
                        'temperature' => $temp,
                        'humidity'   => $humidity,
                        'wind_speed' => $wind,
                        'rainfall'   => $rain,
                        'condition'  => $this->weatherCodeToCondition($data['weather_code'] ?? 0),
                        'logged_at'  => now(),
                    ]);
                }

                // Generate alerts
                $this->generateAlerts($farm, $temp, $humidity, $wind, $rain);

                $this->info("Weather fetched for: {$farm->name}");
            } catch (\Throwable $e) {
                $this->warn("Failed for {$farm->name}: " . $e->getMessage());
            }
        }
    }

    private function generateAlerts(Farm $farm, ?float $temp, ?float $humidity, ?float $wind, ?float $rain): void
    {
        // Frost alert
        if ($temp !== null && $temp < 2) {
            FarmWeatherAlert::create([
                'company_id'    => $farm->company_id,
                'farm_id'       => $farm->id,
                'alert_type'    => 'frost',
                'severity'      => 'critical',
                'title'         => 'Frost Warning',
                'message'       => "Temperature at {$farm->name} is {$temp}°C — frost risk. Protect sensitive crops.",
                'temperature_c' => $temp,
                'triggered_at'  => now(),
            ]);
        }

        // Heat stress alert
        if ($temp !== null && $temp > 38) {
            FarmWeatherAlert::create([
                'company_id'    => $farm->company_id,
                'farm_id'       => $farm->id,
                'alert_type'    => 'heat_stress',
                'severity'      => 'warning',
                'title'         => 'Heat Stress Alert',
                'message'       => "Temperature at {$farm->name} is {$temp}°C — heat stress risk for crops and livestock.",
                'temperature_c' => $temp,
                'triggered_at'  => now(),
            ]);
        }

        // Spray window — good conditions: wind < 15 km/h, humidity 40–85%, no rain
        if ($wind !== null && $humidity !== null && $rain !== null &&
            $wind < 15 && $humidity >= 40 && $humidity <= 85 && $rain < 0.5) {
            FarmWeatherAlert::create([
                'company_id'     => $farm->company_id,
                'farm_id'        => $farm->id,
                'alert_type'     => 'spray_window_open',
                'severity'       => 'info',
                'title'          => 'Spray Window Open',
                'message'        => "Conditions at {$farm->name} are suitable for spraying: wind {$wind} km/h, humidity {$humidity}%.",
                'wind_speed_kmh' => $wind,
                'humidity_pct'   => $humidity,
                'triggered_at'   => now(),
            ]);
        }

        // Heavy rain alert
        if ($rain !== null && $rain > 20) {
            FarmWeatherAlert::create([
                'company_id'   => $farm->company_id,
                'farm_id'      => $farm->id,
                'alert_type'   => 'heavy_rain',
                'severity'     => 'warning',
                'title'        => 'Heavy Rain Alert',
                'message'      => "Heavy rainfall ({$rain}mm) at {$farm->name} — delay field operations.",
                'rainfall_mm'  => $rain,
                'triggered_at' => now(),
            ]);
        }
    }

    private function weatherCodeToCondition(int $code): string
    {
        return match (true) {
            $code === 0                             => 'Clear Sky',
            in_array($code, [1, 2, 3])              => 'Partly Cloudy',
            in_array($code, [51, 53, 55, 61, 63, 65]) => 'Rain',
            in_array($code, [71, 73, 75])           => 'Snow',
            in_array($code, [95, 96, 99])           => 'Thunderstorm',
            default                                 => 'Cloudy',
        };
    }
}