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
                'vehicle' => null,
                'location' => null
            ]);
        }

        $vehicle = $driver->vehicle_info;

        // Retrieve the latest location
        $latestLocation = DB::table('driver_locations')
            ->where('driver_id', $driver->id)
            ->orderBy('updated_at', 'desc')
            ->first();

        return response()->json([
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'is_driver' => $user->is_driver,
            'license_number' => $driver->license_number,
            'vehicle' => $vehicle,
            'location' => $latestLocation ? [
                'lat' => $latestLocation->location->getLat(),
                'lng' => $latestLocation->location->getLng()
            ] : null
        ]);
    }

    public function updateUserWithDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            
            // Driver-specific validations
            'is_driver' => 'sometimes|boolean',
            'license_number' => 'required_if:is_driver,true|unique:drivers,license_number,' . optional(auth()->user()->driver)->id,
            
            // Vehicle validations
            'vehicle' => 'required_if:is_driver,true|array',
            'vehicle.make' => 'required_if:is_driver,true|string',
            'vehicle.model' => 'required_if:is_driver,true|string',
            'vehicle.year' => 'required_if:is_driver,true|integer|min:1900|max:' . (date('Y') + 1),
            'vehicle.color' => 'required_if:is_driver,true|string',
            'vehicle.plate_number' => 'required_if:is_driver,true|string'
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
                    'is_driver' => $request->is_driver ?? false
                ]);

                // Handle driver details if is_driver is true
                if ($request->is_driver) {
                    // Update or create driver
                    $driver = $user->driver ?? new Driver();
                    $driver->user_id = $user->id;
                    $driver->name = $request->name;
                    $driver->license_number = $request->license_number;
                    
                    // Prepare vehicle data
                    $vehicleData = [
                        'make' => $request->vehicle['make'],
                        'model' => $request->vehicle['model'],
                        'year' => $request->vehicle['year'],
                        'color' => $request->vehicle['color'],
                        'plate_number' => $request->vehicle['plate_number'],
                    ];
                    
                    $driver->vehicle_info = $vehicleData;
                    $driver->save();

                    // Update driver verification
                    $user->update([
                        'driver_verified_at' => now()
                    ]);
                }
            });

            return response()->json([
                'message' => 'Profile updated successfully',
                'status' => 'success',
                'user' => auth()->user()->load('driver')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update profile',
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
            'driver_location' => 'nullable|array',
            'driver_location.lat' => 'required_with:driver_location|numeric',
            'driver_location.lng' => 'required_with:driver_location|numeric'
        ]);

        try {
            $driver = auth()->user()->driver;
            $driver->status = $request->status;

            if ($request->has('driver_location')) {
                $driver->last_location = DB::raw("POINT({$request->driver_location['lng']}, {$request->driver_location['lat']})");
                $driver->last_location_updated_at = now();
            }
            $driver->save();

            return response()->json([
                'message' => 'Driver status and location updated successfully',
                'status' => 'success',
                'driver_status' => $driver->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update driver status and location',
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatus()
    {
        try {
            $driver = auth()->user()->driver;
            
            return response()->json([
                'message' => 'Driver status retrieved successfully',
                'status' => 'success',
                'driver_status' => $driver->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get driver status',
                'status' => 'error'
            ], 500);
        }
    }
} 