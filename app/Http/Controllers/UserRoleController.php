<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'accounts', 'profile', 'referral.parent']);
    
        // Search by name or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
    
        $users = $query->orderBy('name')->paginate(10)->withQueryString();
    
        return view('admin.roles', compact('users'));
    }


    public function toggleAdmin(User $user)
    {
        if ($user->hasRole('Admin')) {
            $user->removeRole('Admin');
            $msg = "{$user->name} is no longer an Admin.";
        } else {
            $user->assignRole('Admin');
            $msg = "{$user->name} is now an Admin.";
        }

        return back()->with('status', $msg);
    }

    public function details($id)
    {
        $user = User::with(['accounts', 'profile', 'referral.parent'])->findOrFail($id);

        return view('partial.user-details', compact('user'));
    }


    public function assignReferral(Request $request, User $user)
    {
        $request->validate([
            'referrer_id' => 'required|exists:users,id',
        ]);
    
        $referrer = User::findOrFail($request->referrer_id);
    
        // 1. Prevent assigning user as their own referrer
        if ($user->id === $referrer->id) {
            return back()->with('error', 'A user cannot refer themselves.');
        }
    
        // 2. Prevent circular loop (check if referrer is a child of this user)
        $isDescendant = $this->isDescendant($referrer, $user);
        if ($isDescendant) {
            return back()->with('error', 'Invalid referral: would create a circular tree.');
        }
    
        // 3. Assign referral
        $user->referral()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'parent_id' => $referrer->id,
                'root_id'   => $referrer->referral?->root_id ?? $referrer->id,
            ]
        );
    
        return back()->with('status', "{$user->name} is now referred by {$referrer->name}");
    }
    

    protected function isDescendant(?User $potentialAncestor, User $user): bool
    {
        if (!$potentialAncestor) {
            return false; // reached the root
        }
    
        if (!$potentialAncestor->referral) {
            return false;
        }
    
        if ($potentialAncestor->referral->parent_id === $user->id) {
            return true;
        }
    
        return $this->isDescendant($potentialAncestor->referral->parent, $user);
    }
    
    
    public function referralList(Request $request)
    {
        $query = User::query()->with(['accounts', 'profile']);
    
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('accounts', function ($qa) use ($search) {
                      $qa->where('serial_number', 'like', "%{$search}%");
                  });
            });
        }
    
        $users = $query->orderBy('name')->paginate(7)->withQueryString();
        
    
        return view('partial.referral-list', compact('users')); // 👈 always return partial
    }

    public function removeReferral(User $user)
    {
        if (!$user->referral) {
            return back()->with('error', "{$user->name} does not have a referrer.");
        }
    
        $user->referral->update([
            'parent_id' => null,
            'root_id'   => null,
        ]);
    
        return back()->with('status', "Referrer removed for {$user->name}.");
    }



    
}
