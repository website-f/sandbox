<?php

// app/Http/Controllers/Auth/RegisterPlusController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use App\Models\Referral;
use App\Services\ReferralTreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterPlusController extends Controller
{
    public function show(Request $request){
        return view('auth.register-plus', ['ref' => $request->query('ref')]);
    }

    public function store(Request $request, ReferralTreeService $tree){
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed|min:8',
            'ref'=>'nullable|string|exists:referrals,ref_code',
        ]);

        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
        ]);

        // default role: Entrepreneur
        $role = Role::where('name','Entrepreneur')->first();
        $user->roles()->attach($role);

        // create accounts (inactive by default for self-registered entrepreneurs)
        foreach (['rizqmall','sandbox'] as $type){
            Account::create(['user_id'=>$user->id,'type'=>$type,'active'=>false]);
        }

        // referral handling (optional)
        if (!empty($data['ref'])) {
            $referrerRef = Referral::where('ref_code',$data['ref'])->first();
            if ($referrerRef) {
                $tree->attach($referrerRef->user, $user);
            }
        } else {
            // not included in MLM: still generate personal ref_code for sharing later if they want
            $code = $tree->generateRefCode($user);
            Referral::create([
                'user_id'=>$user->id,
                'parent_id'=>null,
                'root_id'=>null,
                'level'=>1,
                'direct_children'=>0,
                'ref_code'=>$code,
            ]);
        }

        Auth::login($user);
        return redirect()->route('dashboard');
    }
}
