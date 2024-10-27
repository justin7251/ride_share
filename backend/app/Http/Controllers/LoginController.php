<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginVerificationNotification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
            'email' => 'required|email'
        ]);

        // Find or create user
        $user = User::firstOrCreate(
            ['phone' => $request->phone],
            ['email' => $request->email]
        );

        // Send verification code via email
        $user->notify(new LoginVerificationNotification());

        return response()->json([
            'message' => 'Verification code sent to your email'
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
            'verification_code' => 'required|string|size:6'
        ]);

        $user = User::where('phone', $request->phone)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid verification code'
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
            'token' => $token,
            'user' => $user
        ]);
    }
}
