<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->header('X-Tenant-ID');
        $user = $request->user();

        if (! $tenantId) {
            abort(400, 'Missing tenant context.');
        }

        $tenant = Tenant::find($tenantId);
        if (! $tenant) {
            abort(404, 'Tenant not found.');
        }

        if (! $user || ! $user->memberships()->where('tenant_id', $tenantId)->exists()) {
            abort(403, 'Unauthorized tenant access.');
        }

        app()->instance('tenant', $tenant);

        return $next($request);
    }
}
