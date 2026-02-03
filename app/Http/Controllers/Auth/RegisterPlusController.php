<?php

// app/Http/Controllers/Auth/RegisterPlusController.php
namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use App\Models\AccountType;
use App\Models\Profile;
use App\Models\Referral;
use App\Models\Blacklist;
use App\Models\Collection;
use App\Models\CollectionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\ReferralTreeService;

class RegisterPlusController extends Controller
{
    public function show(Request $request){
        return view('auth.register-plus', ['ref' => $request->query('ref')]);
    }

    public function store(Request $request, ReferralTreeService $tree)
    {
        $data = $request->validate([
            // Name: only letters, spaces, apostrophes, dots, and hyphens
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z\s\'.-]+$/u'
            ],
    
            // Email: must be unique, valid format, and domain must have MX records
            'email' => 'required|email:rfc,dns|max:255|unique:users',
    
            // Password: at least 8 chars, must match confirmation
            'password' => 'required|confirmed|min:8',
    
            // Optional referral code (must exist if provided)
            'ref' => 'nullable|string|exists:referrals,ref_code',
    
            // Phone: optional, only digits, spaces, +, -, () allowed, length 8–20
            'phone' => [
                'nullable',
                'regex:/^[0-9+\-()\s]{8,20}$/'
            ],
    
            // Location fields: optional, max 100 chars
            'country' => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'city'    => 'nullable|string|max:100',

            // Date of birth: required for sandbox type validation
            'dob' => 'required|date|before:today',

            // Sandbox type: required, must be one of the valid types
            'sandbox_type' => 'required|in:usahawan,remaja,awam',
        ], [
            // Custom error messages (optional)
            'name.regex'  => 'The name may only contain letters, spaces, apostrophes, dots, and hyphens.',
            'phone.regex' => 'The phone number format is invalid.',
        ]);

        // Age validation for Remaja type (must be 11-20 years old)
        if ($data['sandbox_type'] === 'remaja') {
            $dob = Carbon::parse($data['dob']);
            $age = $dob->age;

            if ($age < 11 || $age > 20) {
                return back()->withErrors([
                    'sandbox_type' => 'Sandbox Remaja is only available for users aged 11-20 years old. Your age: ' . $age . ' years.',
                ])->withInput();
            }
        }

        $blacklistQuery = Blacklist::where('email', $data['email'])
            ->orWhere('name', $data['name'])
            ->orWhere('phone', $request->input('phone'))
            ->first();

        if ($blacklistQuery) {
            $field = '';
            if ($blacklistQuery->email === $data['email']) $field = 'email';
            elseif ($blacklistQuery->name === $data['name']) $field = 'name';
            elseif ($blacklistQuery->phone === $request->input('phone')) $field = 'phone';
        
            return back()->withErrors([
                $field => "This {$field} is blacklisted. Please contact support."
            ])->withInput();
        }
    
        // --- User creation ---
        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'sandbox_type' => $data['sandbox_type'],
        ]);

        Profile::create([
            'user_id'   => $user->id,
            'full_name' => $user->name,
            'dob'       => $data['dob'],
            'phone'     => $data['phone'] ?? null,
            'country'   => $data['country'] ?? null,
            'state'     => $data['state'] ?? null,
            'city'      => $data['city'] ?? null,
        ]);

        // Create corresponding user in RizqMall
        try {
            $rizqmallService = app(\App\Services\RizqmallApiService::class);
            $rizqmallUser = $rizqmallService->createUserInRizqmall([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'country' => $data['country'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'sandbox_user_id' => $user->id,
            ]);

            if ($rizqmallUser) {
                \Log::info('RizqMall account created for Sandbox user', [
                    'sandbox_user_id' => $user->id,
                    'rizqmall_user_id' => $rizqmallUser['id'],
                ]);
            } else {
                \Log::warning('Failed to create RizqMall account for Sandbox user', [
                    'sandbox_user_id' => $user->id,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating RizqMall account', [
                'sandbox_user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            // Don't fail registration if RizqMall creation fails
        }
    
        // default role: Entrepreneur
        $role = Role::where('name', 'Entrepreneur')->first();
        $user->roles()->attach($role);
    
        // create inactive accounts with proper account_type_id and subtype
        $accountTypes = AccountType::whereIn('name', ['rizqmall', 'sandbox'])->get()->keyBy('name');

        // Create RizqMall account
        Account::create([
            'user_id' => $user->id,
            'type' => Account::TYPE_RIZQMALL,
            'account_type_id' => $accountTypes['rizqmall']->id ?? null,
            'active' => false,
        ]);

        // Create Sandbox account with subtype
        Account::create([
            'user_id' => $user->id,
            'type' => Account::TYPE_SANDBOX,
            'subtype' => $data['sandbox_type'],
            'account_type_id' => $accountTypes['sandbox']->id ?? null,
            'active' => false,
        ]);

        // Create collections for this user's sandbox type
        $collectionAccountType = match($data['sandbox_type']) {
            'remaja' => CollectionType::ACCOUNT_SANDBOX_REMAJA,
            'awam' => CollectionType::ACCOUNT_SANDBOX_AWAM,
            default => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
        };
        Collection::createForUser($user->id, $collectionAccountType);

        // referral handling
        if (!empty($data['ref'])) {
            $referrerRef = Referral::where('ref_code',$data['ref'])->first();
            if ($referrerRef) {
                $tree->attach($referrerRef->user, $user);
            }
        } else {
            $code = $tree->generateRefCode($user);
            Referral::create([
                'user_id'   => $user->id,
                'parent_id' => null,
                'root_id'   => null,
                'level'     => 1,
                'direct_children' => 0,
                'ref_code'  => $code,
            ]);
        }
    
        Auth::login($user);
        return redirect()->route('dashboard');
    }

}