<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\RideStarted;
use App\Events\RideCompleted;
use App\Events\RideLocationUpdated;
use App\Events\RideAccepted;

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
                    DB::raw('ST_Distance_Sphere(
                        last_location,
                        POINT(?, ?)
                    ) as distance_meters'
                    ),
                    'users.name'
                )
                ->join('users', 'drivers.user_id', '=', 'users.id')
                ->having('distance_meters', '<', 5000)
                ->orderBy('distance_meters', 'asc')
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

    public function store(Request $request)
    {
        $existingRide = Ride::where('user_id', auth()->id())
            ->where('is_complete', false)
            ->first();

        if ($existingRide) {
            return response()->json([
                'message' => 'You already have an active ride',
                'ride' => $existingRide->load(['user', 'driver'])
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'origin' => 'required|array',
            'destination' => 'required|array',
            'destination_name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ride = Ride::create([
            'user_id' => auth()->id(),
            'driver_id' => $request->user()->id,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'destination_name' => $request->destination_name
        ]);

        return response()->json($ride->load(['user', 'driver']), 201);
    }

    public function show(Request $request, Ride $ride)
    {
        if ($ride->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($ride->is_complete) {
            return response()->json(['message' => 'Ride is complete'], 403);
        }

        return $ride;
    }

    public function update(Request $request, Ride $ride)
    {
        $validator = Validator::make($request->all(), [
            'is_started' => 'boolean',
            'is_complete' => 'boolean',
            'driver_location' => 'array',
            'driver_location.lat' => 'required_with:driver_location|numeric',
            'driver_location.lng' => 'required_with:driver_location|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ride->update($request->all());
        return response()->json($ride->load(['user', 'driver']));
    }

    public function accept(Request $request, Ride $ride)
    {
        if (!$request->user()->driver) {
            return response()->json(['message' => 'User is not a driver'], 403);
        }

        if ($ride->driver_id) {
            return response()->json(['message' => 'Ride already accepted'], 403);
        }

        $ride->update(['driver_id' => $request->user()->id]);
        
        // Dispatch the event
        event(new RideAccepted($ride));
        
        return response()->json($ride->load(['user', 'driver']));
    }

    public function start(Request $request, Ride $ride)
    {
        if ($ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$ride->driver_id) {
            return response()->json(['message' => 'Ride not accepted'], 403);
        }

        $ride->update(['is_started' => true]);
        
        // Dispatch the event
        event(new RideStarted($ride));
        
        return response()->json($ride->load(['user', 'driver']));
    }

    public function complete(Request $request, Ride $ride)
    {
        if ($ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$ride->is_started) {
            return response()->json(['message' => 'Ride not started'], 403);
        }

        $ride->update(['is_complete' => true]);
        
        event(new rideCompleted($ride));
        
        return response()->json($ride->load(['user', 'driver']));
    }

    public function updateDriverLocation(Request $request, Ride $ride)
    {
        if ($ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'driver_location' => 'required|array',
            'driver_location.lat' => 'required|numeric',
            'driver_location.lng' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $ride->update(['driver_location' => $request->driver_location]);
        
        event(new RideLocationUpdated($ride, $request->driver_location));
        
        return response()->json($ride);
    }
} 