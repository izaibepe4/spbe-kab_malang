<?php

use App\Services\ApiHealthMonitor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health Check Endpoint
Route::get('/health', function () {
    $monitor = new ApiHealthMonitor;

    return response()->json($monitor->getAllHealthStatus());
})->name('api.health');

// Health Check untuk specific service
Route::get('/health/{service}', function (string $service) {
    $monitor = new ApiHealthMonitor;
    $endpoints = config('monitoring.api_endpoints', []);

    if (! isset($endpoints[$service])) {
        return response()->json([
            'error' => 'Service not found',
        ], 404);
    }

    $health = $monitor->checkHealth($service, $endpoints[$service]);
    $metrics = $monitor->getHealthMetrics($service);

    return response()->json([
        'health' => $health,
        'metrics' => $metrics,
    ]);
})->name('api.health.service');

// Health History
Route::get('/health/{service}/history', function (string $service) {
    $monitor = new ApiHealthMonitor;

    return response()->json([
        'service' => $service,
        'history' => $monitor->getHistory($service, 50),
    ]);
})->name('api.health.history');
