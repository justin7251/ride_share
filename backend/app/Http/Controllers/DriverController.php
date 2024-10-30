<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        return response()->json($driver->load('user'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'license_number' => 'string|unique:drivers,license_number,' . $driver->id,
            'status' => 'in:active,inactive',
            'vehicle_info' => 'array',
            'vehicle_info.year' => 'integer|min:1900|max:' . (date('Y') + 1),
            'vehicle_info.make' => 'string',
            'vehicle_info.model' => 'string',
            'vehicle_info.color' => 'string',
            'vehicle_info.plate_number' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $driver->update($request->all());
        return response()->json($driver);
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return response()->json(null, 204);
    }
} 