<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    public function store(Request $request)
    {
        $existingTrip = Trip::where('user_id', auth()->id())
            ->where('is_complete', false)
            ->first();

        if ($existingTrip) {
            return response()->json([
                'message' => 'You already have an active trip',
                'trip' => $existingTrip->load(['user', 'driver'])
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

        $trip = Trip::create([
            'user_id' => auth()->id(),
            'driver_id' => $request->user()->id,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'destination_name' => $request->destination_name
        ]);

        return response()->json($trip->load(['user', 'driver']), 201);
    }

    public function show(Request $request, Trip $trip)
    {
        // is the trip assoicate to the user?
        if ($trip->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($trip->is_complete) {
            return response()->json(['message' => 'Trip is complete'], 403);
        }

        return $trip;
    }

    public function update(Request $request, Trip $trip)
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

        $trip->update($request->all());
        return response()->json($trip->load(['user', 'driver']));
    }

    public function accept(Request $request, Trip $trip)
    {
        if ($trip->driver_id) {
            return response()->json(['message' => 'Trip already accepted'], 403);
        }

        $trip->update(['driver_id' => $request->user()->id]);
        return response()->json($trip->load(['user', 'driver']));
    }

    public function start(Request $request, Trip $trip)
        {
        if (!$trip->driver_id) {
            return response()->json(['message' => 'Trip not accepted'], 403);
        }

        $trip->update(['is_started' => true]);
        return response()->json($trip->load(['user', 'driver']));
    }

    public function complete(Request $request, Trip $trip)
    {
        if (!$trip->is_started) {
            return response()->json(['message' => 'Trip not started'], 403);
        }

        $trip->update(['is_complete' => true]);
        return response()->json($trip->load(['user', 'driver']));
    }   

    public function updateDriverLocation(Request $request, Trip $trip)
    {
        $validator = Validator::make($request->all(), [
            'driver_location' => 'required|array',
            'driver_location.lat' => 'required|numeric',
            'driver_location.lng' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        return response()->json($trip);
    }
}
