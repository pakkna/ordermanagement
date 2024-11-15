<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminRoleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        // Check if user is authenticated and has the 'admin' role
        if ($user && $user->hasRole('admin')) {
            return $next($request); // Proceed if the user is an admin
        }

        // Reject if not an admin
        return response()->json(['message' => 'Unauthorized. You do not have the required role.'], 403);
    }
}
