<?php

// app/Http/Controllers/Auth/RegisterPlusController.php
namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use App\Models\Profile;
use App\Models\Referral;
use App\Models\Blacklist;
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
        ], [
            // Custom error messages (optional)
            'name.regex'  => 'The name may only contain letters, spaces, apostrophes, dots, and hyphens.',
            'phone.regex' => 'The phone number format is invalid.',
        ]);

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
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    
        Profile::create([
            'user_id'   => $user->id,
            'full_name' => $user->name,
            'phone'     => $data['phone'] ?? null,
            'country'   => $data['country'] ?? null,
            'state'     => $data['state'] ?? null,
            'city'      => $data['city'] ?? null,
        ]);
    
        // default role: Entrepreneur
        $role = Role::where('name', 'Entrepreneur')->first();
        $user->roles()->attach($role);
    
        // create inactive accounts
        foreach (['rizqmall','sandbox'] as $type){
            Account::create(['user_id' => $user->id, 'type' => $type, 'active' => false]);
        }
    
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
