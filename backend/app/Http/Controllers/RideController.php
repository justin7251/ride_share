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
use App\Events\RideRequestEvent;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    public function searchDrivers(Request $request)
    {
        $request->validate([
            'pickup' => 'required|string',
            'destination' => 'required|string',
            'pickupLat' => 'required|numeric',
            'pickupLng' => 'required|numeric',
            'timestamp' => 'nullable|date'
        ]);

        try {
            // Add the ride request to a holding tank
            $rideRequest = Ride::firstOrCreate(
                [
                    'user_id' => auth()->id(),
                    'origin' => $request->pickup,
                    'destination' => $request->destination,
                    'pickup_lat' => $request->pickupLat,
                    'pickup_lng' => $request->pickupLng,
                    'destination_lat' => $request->destinationLat,
                    'destination_lng' => $request->destinationLng
                ], // Search attributes
                [
                    'driver_id' => null
                ]
            );

            // Notify available drivers
            $activeDrivers = Driver::where('status', 'active')
                ->whereNull('current_ride_id')
                ->where('last_location_updated_at', '>', now()->subMinutes(30))
                ->get();

            broadcast(new RideRequestEvent($rideRequest, $activeDrivers));
 
            return response()->json([
                'success' => true,
                'rideRequestId' => $rideRequest->id,
                'drivers' => $activeDrivers
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

        return Ride::transaction(function () use ($ride) {
            $ride->update($request->all());
            return response()->json($ride->load(['user', 'driver']));
        });
    }

    public function accept(Request $request, Ride $ride)
    {
        if (!$request->user()->driver) {
            return response()->json(['message' => 'User is not a driver'], 403);
        }

        if ($ride->driver_id) {
            return response()->json(['message' => 'Ride already accepted'], 403);
        }

        if ($ride->is_complete || $ride->is_started) {
            return response()->json(['message' => 'Ride cannot be accepted at this stage'], 403);
        }

        return Ride::transaction(function () use ($ride) {
            $ride->update(['driver_id' => $request->user()->id]);
            event(new RideAccepted($ride));
            return response()->json($ride->load(['user', 'driver']), 200);
        });
    }

    public function start(Request $request, Ride $ride)
    {
        if ($ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$ride->driver_id) {
            return response()->json(['message' => 'Ride not accepted'], 403);
        }

        return Ride::transaction(function () use ($ride) {
            $ride->update(['is_started' => true]);
            event(new RideStarted($ride));
            return response()->json($ride->load(['user', 'driver']), 200);
        });
    }

    public function complete(Request $request, Ride $ride)
    {
        if ($ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$ride->is_started) {
            return response()->json(['message' => 'Ride not started'], 403);
        }

        return Ride::transaction(function () use ($ride) {
            $ride->update(['is_complete' => true]);
            event(new RideCompleted($ride));
            return response()->json($ride->load(['user', 'driver']), 200);
        });
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

        return Ride::transaction(function () use ($ride, $request) {
            $ride->update(['driver_location' => $request->driver_location]);
            event(new RideLocationUpdated($ride, $request->driver_location));
            return response()->json($ride);
        });
    }

    public function cancel(Request $request, Ride $ride)
    {
        // Ensure only the ride requester can cancel
        if ($ride->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Prevent cancellation of started or completed rides
        if ($ride->is_started || $ride->is_complete) {
            return response()->json(['message' => 'Ride cannot be cancelled at this stage'], 403);
        }

        return DB::transaction(function () use ($ride) {
            $ride->update([
                'status' => 'cancelled',
                'driver_id' => null
            ]);
            // TODO: Broadcast cancellation event
            // event(new RideCancelled($ride));

            return response()->json([
                'success' => true,
                'message' => 'Ride cancelled successfully'
            ]);
        });
    }

    public function track(Request $request, Ride $ride)
    {
        // Ensure only the ride requester or driver can track
        if ($ride->user_id !== $request->user()->id && $ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Load related driver and user information
        $rideDetails = $ride->load(['driver', 'user']);

        return response()->json([
            'ride' => $rideDetails,
            'status' => $this->determineRideStatus($ride),
            'driver' => $ride->driver ? [
                'name' => $ride->driver->name,
                'vehicle' => $ride->driver->vehicle_details,
                'rating' => $ride->driver->rating
            ] : null,
            'location' => $ride->driver_location,
            'eta' => $this->calculateETA($ride),
            'distance' => $this->calculateDistance($ride)
        ]);
    }

    // Helper methods
    private function determineRideStatus(Ride $ride)
    {
        if ($ride->is_complete) return 'Completed';
        if ($ride->is_started) return 'In Progress';
        if ($ride->driver_id) return 'Driver Assigned';
        return 'Searching for Driver';
    }

    private function calculateETA(Ride $ride)
    {
        // Implement your ETA calculation logic
        return 15; // Default 15 minutes
    }

    private function calculateDistance(Ride $ride)
    {
        // Implement distance calculation logic
        return $this->haversineDistance(
            $ride->pickup_lat, 
            $ride->pickup_lng, 
            $ride->destination_lat, 
            $ride->destination_lng
        );
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Haversine formula implementation
        $earthRadius = 6371; // kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
} 