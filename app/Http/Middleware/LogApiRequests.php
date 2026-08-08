<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogApiRequests
{
    public function handle($request, Closure $next)
    {
        $requestId = (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);

        $start = microtime(true);

        Log::info('API request received', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);

        $response = $next($request);

        Log::info('API request completed', [
            'request_id' => $requestId,
            'status' => $response->getStatusCode(),
            'duration_ms' => round((microtime(true) - $start) * 1000),
        ]);

        return $response;
    }
}