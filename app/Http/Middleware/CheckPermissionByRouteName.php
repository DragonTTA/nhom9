<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissionByRouteName
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $routeName = $request->route()->getName();
        if (!$routeName) {
            return $next($request);
        }
        if ($user->can($routeName)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden - no permission for ' . $routeName], 403);
    }
}
