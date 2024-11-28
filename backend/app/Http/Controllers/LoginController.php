<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginVerificationNotification;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:7|max:15',
        ]);

        // Find existing user
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Phone number not registered',
                'status' => 'error'
            ], 404);
        }

        $this->sendVerificationCode($user);

        return response()->json([
            'message' => 'Verification code sent to your email',
            'status' => 'success'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:7|max:15|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'name' => $request->name
                ]);
    
                Driver::create([
                    'user_id' => $user->id,
                    'name' => $request->name,
                    'last_location' => null
                ]);

                $this->sendVerificationCode($user);
            });
    
            return response()->json([
                'message' => 'Registration successful.',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:7|max:15',
            'verification_code' => 'required|string|size:6'
        ]);

        $user = User::where('phone', $request->phone)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid verification code',
                'status' => 'error'
            ], 422);
        }

        // Mark phone as verified
        $user->phone_verified_at = now();
        $user->verification_code = null;
        $user->save();

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Successfully verified',
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }

    private function sendVerificationCode(User $user)
{
    $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $user->verification_code = $verificationCode;
    $user->save();

    // Send notification with error handling
    try {
        $user->notify(new LoginVerificationNotification());
    } catch (\Exception $e) {
        // Log the error or handle it as needed
        // You can also return a response or throw a custom exception
        throw new \Exception('Failed to send verification code: ' . $e->getMessage());
    }
}
}
