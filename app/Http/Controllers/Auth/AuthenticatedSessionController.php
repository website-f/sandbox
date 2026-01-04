<?php

namespace App\Http\Controllers\Auth;

use App\Models\Blacklist;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Attempt login
        $request->authenticate();
    
        $user = Auth::user();
    
        // 🚫 Check if email is blacklisted
        if (Blacklist::where('email', $user->email)->exists()) {
            Auth::logout(); // immediately log them out
    
            return back()->withErrors([
                'email' => 'Your email is blacklisted. You cannot log in.',
            ])->withInput($request->only('email'));
        }
    
        // ✅ Normal login
        $request->session()->regenerate();

        // Auto-link/create RizqMall account for existing Sandbox users
        $this->ensureRizqmallAccountExists($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Ensure RizqMall account exists for existing Sandbox users
     * This handles users who registered before the unified system
     */
    private function ensureRizqmallAccountExists($user)
    {
        try {
            \Log::info('Checking if RizqMall account needs linking', [
                'sandbox_user_id' => $user->id,
                'email' => $user->email,
            ]);

            $rizqmallService = app(\App\Services\RizqmallApiService::class);

            // Check if RizqMall account already exists for this user
            $rizqmallUser = $rizqmallService->findUserByEmail($user->email);

            if ($rizqmallUser && !$rizqmallUser['subscription_user_id']) {
                // RizqMall user exists but not linked - link it
                $rizqmallService->linkUserToSandbox($rizqmallUser['id'], $user->id);

                \Log::info('Linked existing RizqMall account to Sandbox', [
                    'sandbox_user_id' => $user->id,
                    'rizqmall_user_id' => $rizqmallUser['id'],
                ]);
            } elseif (!$rizqmallUser) {
                // Create new RizqMall account
                $rizqmallUser = $rizqmallService->createUserInRizqmall([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->profile->phone ?? null,
                    'country' => $user->profile->country ?? null,
                    'state' => $user->profile->state ?? null,
                    'city' => $user->profile->city ?? null,
                    'sandbox_user_id' => $user->id,
                ]);

                \Log::info('Created RizqMall account for existing Sandbox user', [
                    'sandbox_user_id' => $user->id,
                    'rizqmall_user_id' => $rizqmallUser['id'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to ensure RizqMall account exists', [
                'sandbox_user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            // Don't fail login if RizqMall sync fails
        }
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
