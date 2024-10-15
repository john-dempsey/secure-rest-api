<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLoggingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);

        $executionTime = $endTime - $startTime;

        Log::info('Request:', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->header(),
            'query' => $request->query(),
            'body' => $request->input(),
            'response_status' => $response->getStatusCode(),
            'execution_time' => $executionTime,
        ]);

        return $response;
    }
}
