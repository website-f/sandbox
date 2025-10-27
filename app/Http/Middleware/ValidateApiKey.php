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
        $apiKey = $request->header('X-API-Key') ?? $request->header('Authorization');
        
        // Remove "Bearer " prefix if present
        if (str_starts_with($apiKey, 'Bearer ')) {
            $apiKey = substr($apiKey, 7);
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