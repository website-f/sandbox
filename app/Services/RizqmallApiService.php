<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RizqmallApiService
{
    protected $baseUrl;
    protected $apiKey;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.rizqmall.url');
        $this->apiKey = config('services.rizqmall.api_key', 'sandbox-api-key-123');
        $this->timeout = config('services.rizqmall.timeout', 30);
    }

    /**
     * Find user in RizqMall by email
     */
    public function findUserByEmail(string $email)
    {
        try {
            Log::info('Looking up RizqMall user by email', [
                'email' => $email,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl . '/api/sandbox/user-by-email', [
                    'email' => $email,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] ?? false) {
                    Log::info('Found RizqMall user by email', [
                        'email' => $email,
                        'rizqmall_user_id' => $data['user']['id'] ?? null,
                    ]);

                    return $data['user'];
                }
            }

            Log::info('No RizqMall user found with email', [
                'email' => $email,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error finding RizqMall user by email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Link RizqMall user to Sandbox user
     */
    public function linkUserToSandbox(int $rizqmallUserId, int $sandboxUserId)
    {
        try {
            Log::info('Linking RizqMall user to Sandbox', [
                'rizqmall_user_id' => $rizqmallUserId,
                'sandbox_user_id' => $sandboxUserId,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . '/api/sandbox/link-user', [
                    'rizqmall_user_id' => $rizqmallUserId,
                    'sandbox_user_id' => $sandboxUserId,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] ?? false) {
                    Log::info('Successfully linked RizqMall user to Sandbox', [
                        'rizqmall_user_id' => $rizqmallUserId,
                        'sandbox_user_id' => $sandboxUserId,
                    ]);

                    return true;
                }
            }

            Log::error('Failed to link RizqMall user to Sandbox', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Error linking RizqMall user to Sandbox', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Create user in RizqMall when they register in Sandbox
     */
    public function createUserInRizqmall(array $userData)
    {
        try {
            Log::info('Creating user in RizqMall via API', [
                'email' => $userData['email'] ?? null,
            ]);

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($this->baseUrl . '/api/sandbox/create-user', $userData);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success'] ?? false) {
                    Log::info('User created successfully in RizqMall', [
                        'rizqmall_user_id' => $data['user']['id'] ?? null,
                        'email' => $userData['email'] ?? null,
                    ]);

                    return $data['user'];
                }
            }

            Log::error('Failed to create user in RizqMall', [
                'status' => $response->status(),
                'response' => $response->json(),
                'email' => $userData['email'] ?? null,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Error creating user in RizqMall', [
                'error' => $e->getMessage(),
                'email' => $userData['email'] ?? null,
            ]);
            return null;
        }
    }
}
