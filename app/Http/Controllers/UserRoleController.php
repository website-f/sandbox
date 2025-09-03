<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

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
}
