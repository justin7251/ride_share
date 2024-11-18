<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RideController extends Controller
{
    public function searchDrivers(Request $request)
    {
        $request->validate([
            'pickup' => 'required|string',
            'destination' => 'required|string',
            'timestamp' => 'nullable|date'
        ]);

        try {
            // Find active drivers within a reasonable radius (e.g., 5km) of pickup
            $activeDrivers = Driver::where('status', 'active')
                ->whereNull('current_ride_id')
                ->where('last_location_updated_at', '>', now()->subMinutes(5))
                ->select(
                    'drivers.*',
                    // DB::raw('ST_Distance_Sphere(
                    //     last_location,
                    //     POINT(?, ?)
                    // ) as distance_meters'
                    // ),
                    'users.name'
                )
                ->join('users', 'drivers.user_id', '=', 'users.id')
                // ->having('distance_meters', '<', 5000) // 5km radius
                // ->orderBy('distance_meters', 'asc')
                ->limit(5)
                ->get();

            if ($activeDrivers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No drivers available in your area'
                ]);
            }

            $drivers = $activeDrivers->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'photo' => $driver->photo,
                    'rating' => $driver->rating,
                    'totalRides' => $driver->total_rides,
                    'distance' => round($driver->distance_meters / 1000, 1), // Convert to km
                    'estimatedArrival' => now()->addMinutes(ceil($driver->distance_meters / 500)), // Rough ETA calculation
                    'vehicle' => [
                        'model' => $driver->vehicle_model,
                        'color' => $driver->vehicle_color,
                        'plate' => $driver->vehicle_plate
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'drivers' => $drivers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search for drivers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 