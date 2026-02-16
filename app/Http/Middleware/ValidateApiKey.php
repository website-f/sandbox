<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        $authorization = $request->header('Authorization');

        if (is_string($apiKey)) {
            $apiKey = trim($apiKey);
        }

        if (!$apiKey && is_string($authorization)) {
            $authorization = trim($authorization);
            // Support bearer token style header.
            if (str_starts_with($authorization, 'Bearer ')) {
                $apiKey = substr($authorization, 7);
            } else {
                $apiKey = $authorization;
            }
        }

        $expectedKey = config('services.rizqmall.api_key');

        if (!$apiKey || $apiKey !== $expectedKey) {
            Log::warning('Invalid API key attempt', [
                'ip' => $request->ip(),
                'key_provided' => $apiKey ? substr($apiKey, 0, 10) . '...' : 'none',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API key.',
            ], 401);
        }

        return $next($request);
    }
}

// Register in app/Http/Kernel.php:
// protected $middlewareAliases = [
//     'api.key' => \App\Http\Middleware\ValidateApiKey::class,
// ];
