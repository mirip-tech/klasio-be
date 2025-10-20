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
        [$token, $memberships] = DB::transaction(function () use ($request) {
            $user = $request->authenticate();

            $memberships = $user->memberships()
                ->where('is_active', true)
                ->with('tenant:id,name,slug') // hanya ambil field penting dari tenant
                ->get(['tenant_id', 'role'])
                ->map(fn ($m) => [
                    'tenant_id' => $m->tenant_id,
                    'tenant' => $m->tenant,
                    'role' => $m->role,
                ])
                ->values();

            $user->tokens()->delete();
            $token = $user->createToken($request->device_name)->plainTextToken;

            return [$token, $memberships];
        });

        return response()->json([
            'token' => $token,
            'memberships' => $memberships,
        ]);
    }

    /**
     * Destroy current an authenticated token.
     */
    public function destroy(Request $request)
    {
        DB::transaction(fn () => $request->user()->currentAccessToken()->delete());

        return response()->json(['message' => 'Logged out']);
    }
}
