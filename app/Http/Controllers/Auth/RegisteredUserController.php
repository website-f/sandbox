<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use App\Models\Profile;
use App\Models\Referral;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'dob' => ['required', 'date', 'before:today'],
            'sandbox_type' => ['required', 'in:usahawan,remaja,awam'],
            'referral_code' => ['nullable', 'string', 'max:50'],
        ]);

        // Validate age for remaja (must be 11-20 years old)
        $sandboxType = $request->sandbox_type;
        if ($sandboxType === 'remaja') {
            $dob = Carbon::parse($request->dob);
            $age = $dob->age;
            if ($age < 11 || $age > 20) {
                return back()->withInput()->withErrors([
                    'sandbox_type' => 'Sandbox Remaja is only available for users aged 11-20 years old.',
                ]);
            }
        }

        // Find referrer if referral code provided
        $referrerId = null;
        if ($request->referral_code) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
            if ($referrer) {
                $referrerId = $referrer->id;
            }
        }

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'referral_code' => $this->generateReferralCode(),
                'sandbox_type' => $sandboxType,
            ]);

            // Create profile with DOB
            Profile::create([
                'user_id' => $user->id,
                'dob' => $request->dob,
            ]);

            // Create referral relationship if referrer exists
            if ($referrerId) {
                Referral::create([
                    'user_id' => $user->id,
                    'parent_id' => $referrerId,
                ]);
            }

            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Registration failed: ' . $e->getMessage());
            return back()->withInput()->withErrors([
                'email' => 'Registration failed. Please try again.',
            ]);
        }
    }

    /**
     * Generate unique referral code
     */
    private function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
