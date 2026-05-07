<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\WeatherData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeatherController extends Controller
{
    /**
     * Display weather data for a farm.
     */
    public function index(Request $request): View
    {
        $query = WeatherData::with(['farm']);

        if ($request->filled('farm_id')) {
            $query->where('farm_id', $request->farm_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('recorded_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('recorded_at', '<=', $request->to_date);
        }

        if ($request->filled('condition')) {
            $query->where('weather_condition', $request->condition);
        }

        $weatherData = $query->orderBy('recorded_at', 'desc')->paginate(24);
        $farms = Farm::all();

        return view('weather.index', compact('weatherData', 'farms'));
    }

    /**
     * Show current weather for a farm.
     */
    public function current(Farm $farm): View
    {
        $currentWeather = WeatherData::where('farm_id', $farm->id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $todayWeather = WeatherData::where('farm_id', $farm->id)
            ->whereDate('recorded_at', now()->toDateString())
            ->orderBy('recorded_at', 'desc')
            ->get();

        $weekWeather = WeatherData::where('farm_id', $farm->id)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->recorded_at->format('Y-m-d');
            });

        return view('weather.current', compact('farm', 'currentWeather', 'todayWeather', 'weekWeather'));
    }

    /**
     * Show weather forecast.
     */
    public function forecast(Farm $farm): View
    {
        // In a real application, this would call a weather API
        // For now, we'll show historical data as forecast
        $historicalData = WeatherData::where('farm_id', $farm->id)
            ->where('recorded_at', '>=', now()->subDays(14))
            ->orderBy('recorded_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->recorded_at->format('Y-m-d');
            });

        return view('weather.forecast', compact('farm', 'historicalData'));
    }

    /**
     * Store new weather data (for API/webhook integration).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'recorded_at' => 'required|date',
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric|min:0|max:100',
            'precipitation' => 'nullable|numeric|min:0',
            'wind_speed' => 'nullable|numeric|min:0',
            'wind_direction' => 'nullable|numeric|min:0|max:360',
            'pressure' => 'nullable|numeric',
            'uv_index' => 'nullable|numeric|min:0',
            'cloud_cover' => 'nullable|numeric|min:0|max:100',
            'dew_point' => 'nullable|numeric',
            'weather_condition' => 'nullable|string|max:50',
            'feels_like' => 'nullable|numeric',
            'visibility' => 'nullable|numeric|min:0',
        ]);

        WeatherData::create($validated);

        return redirect()->back()->with('success', 'Weather data recorded successfully.');
    }

    /**
     * Get weather statistics for a period.
     */
    public function statistics(Request $request): View
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
        ]);

        $weatherData = WeatherData::where('farm_id', $request->farm_id)
            ->whereBetween('recorded_at', [$request->from_date, $request->to_date])
            ->get();

        $stats = [
            'avg_temperature' => $weatherData->avg('temperature'),
            'avg_humidity' => $weatherData->avg('humidity'),
            'total_precipitation' => $weatherData->sum('precipitation'),
            'avg_wind_speed' => $weatherData->avg('wind_speed'),
            'avg_uv_index' => $weatherData->avg('uv_index'),
            'recordings_count' => $weatherData->count(),
        ];

        $farm = Farm::find($request->farm_id);

        return view('weather.statistics', compact('farm', 'stats', 'weatherData'));
    }

    /**
     * Get latest weather for dashboard.
     */
    public function getLatestWeather($farmId)
    {
        return WeatherData::where('farm_id', $farmId)
            ->orderBy('recorded_at', 'desc')
            ->first();
    }

    /**
     * Get weather alerts based on conditions.
     */
    public function getAlerts($farmId)
    {
        $latestWeather = $this->getLatestWeather($farmId);

        if (! $latestWeather) {
            return [];
        }

        $alerts = [];

        // High temperature alert
        if ($latestWeather->temperature > 35) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'High Temperature',
                'message' => "Current temperature is {$latestWeather->temperature}°C. Ensure adequate irrigation.",
            ];
        }

        // Low temperature alert
        if ($latestWeather->temperature < 5) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Temperature',
                'message' => "Current temperature is {$latestWeather->temperature}°C. Protect sensitive crops.",
            ];
        }

        // High wind alert
        if ($latestWeather->wind_speed > 10) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'High Wind',
                'message' => "Wind speed is {$latestWeather->wind_speed} m/s. Avoid spraying operations.",
            ];
        }

        // Rain alert
        if ($latestWeather->precipitation > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Rain Detected',
                'message' => "Precipitation recorded: {$latestWeather->precipitation} mm.",
            ];
        }

        return $alerts;
    }
}
