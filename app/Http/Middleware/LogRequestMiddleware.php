<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RequestLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LogRequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        try {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $userId = Auth::id();

            $abc = RequestLog::create([
                'user_id' => $userId,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'status_code' => $response->getStatusCode(),
                'response_time' => $duration,
                'request_data' => $request->except(['password', 'password_confirmation']),
                'response_data' => json_decode($response->getContent(), true),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to write request log: ' . $e->getMessage());
        }

        return $response;
    }
}
