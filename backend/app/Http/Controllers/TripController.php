<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\RideStarted;
use App\Events\RideCompleted;
use App\Events\RideLocationUpdated;
use App\Events\RideAccepted;

class RideController extends Controller
{
    public function store(Request $request)
    {
        $existingride = ride::where('user_id', auth()->id())
            ->where('is_complete', false)
            ->first();

        if ($existingride) {
            return response()->json([
                'message' => 'You already have an active ride',
                'ride' => $existingride->load(['user', 'driver'])
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

        $ride = ride::create([
            'user_id' => auth()->id(),
            'driver_id' => $request->user()->id,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'destination_name' => $request->destination_name
        ]);

        return response()->json($ride->load(['user', 'driver']), 201);
    }

    public function show(Request $request, ride $ride)
    {
        // is the ride assoicate to the user?
        if ($ride->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($ride->is_complete) {
            return response()->json(['message' => 'ride is complete'], 403);
        }

        return $ride;
    }

    public function update(Request $request, ride $ride)
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

    public function accept(Request $request, ride $ride)
    {
        if (!$request->user()->driver) {
            return response()->json(['message' => 'User is not a driver'], 403);
        }

        if ($ride->driver_id) {
            return response()->json(['message' => 'ride already accepted'], 403);
        }

        $ride->update(['driver_id' => $request->user()->id]);
        
        // Dispatch the event
        event(new rideAccepted($ride));
        
        return response()->json($ride->load(['user', 'driver']));
    }

    public function start(Request $request, ride $ride)
    {
        if ($ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$ride->driver_id) {
            return response()->json(['message' => 'ride not accepted'], 403);
        }

        $ride->update(['is_started' => true]);
        
        // Dispatch the event
        event(new rideStarted($ride));
        
        return response()->json($ride->load(['user', 'driver']));
    }

    public function complete(Request $request, ride $ride)
    {
        if ($ride->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$ride->is_started) {
            return response()->json(['message' => 'ride not started'], 403);
        }

        $ride->update(['is_complete' => true]);
        
        // Dispatch the event
        event(new rideCompleted($ride));
        
        return response()->json($ride->load(['user', 'driver']));
    }

    public function updateDriverLocation(Request $request, ride $ride)
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
        
        // Dispatch the event
        event(new rideLocationUpdated($ride, $request->driver_location));
        
        return response()->json($ride);
    }
}
