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
    
        return redirect()->intended(route('dashboard', absolute: false));
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
