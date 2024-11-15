<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return response()->json($drivers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|unique:drivers',
            'status' => 'required|in:active,inactive',
            'vehicle_info' => 'required|array',
            'vehicle_info.year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_info.make' => 'required|string',
            'vehicle_info.model' => 'required|string',
            'vehicle_info.color' => 'required|string',
            'vehicle_info.plate_number' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $driver = Driver::create([
            'user_id' => auth()->id(),  // Get authenticated user's ID
            'name' => $request->name,
            'license_number' => $request->license_number,
            'status' => $request->status,
            'vehicle_info' => $request->vehicle_info
        ]);

        return response()->json($driver->load('user'), 201);
    }

    public function show(Request $request)
    {
        $driver = Driver::where('user_id', $request->user()->id)->first();
        
        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }
        
        return response()->json($driver->load('user'));
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return response()->json(null, 204);
    }

    public function getUserWithDriver(Request $request)
    {
        $user = $request->user();
        $driver = $user->driver;

        if (!$driver) {
            return response()->json([
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'license_number' => null,
                'vehicle' => null
            ]);
        }

        return response()->json([
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'license_number' => $driver->license_number,
            'vehicle' => $driver->vehicle_info
        ]);
    }

    public function updateUserWithDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'license_number' => 'required|string|unique:drivers,license_number,' . optional(auth()->user()->driver)->id,
            'vehicle' => 'required|array',
            'vehicle.make' => 'required|string',
            'vehicle.model' => 'required|string',
            'vehicle.year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                // Update user
                $user = auth()->user();
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);

                // Update or create driver
                $driver = $user->driver ?? new Driver();
                $driver->user_id = $user->id;
                $driver->name = $request->name;
                $driver->license_number = $request->license_number;
                $driver->vehicle_info = $request->vehicle;
                $driver->save();
            });

            return response()->json([
                'message' => 'Profile updated successfully',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'status' => 'error'
            ], 500);
        }
    }
} 