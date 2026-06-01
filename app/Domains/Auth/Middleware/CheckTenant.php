<?php

namespace App\Domains\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->tenant_id) {
            return redirect()->route('register')
                ->with('error', 'No tenant assigned to your account. Please register a restaurant.');
        }

        config(['current_tenant_id' => $user->tenant_id]);

        return $next($request);
    }
}
