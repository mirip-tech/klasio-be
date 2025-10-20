<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthenticatedTokenController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $token = DB::transaction(function () use ($request) {
            $user = $request->authenticate();
            return $user->createToken($request->device_name)->plainTextToken;
        });

        return response()->json(['token' => $token]);
    }

    /**
     * Destroy current an authenticated token.
     */
    public function destroy(Request $request)
    {
        DB::transaction(fn() => $request->user()->currentAccessToken()->delete());
        return response()->json(['message' => 'Logged out']);
    }
}
