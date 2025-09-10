<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller {
    public function index(Request $req) {
        $user = $req->user();
        $accounts = $user->accounts->keyBy('type');

        // Start query
        $query = User::with(['accounts', 'profile', 'referral.parent', 'referral']);

        if (!$user->hasRole('Admin')) {
            // Non-admin: only see their own downline
            $query->whereHas('referral', function ($q) use ($user) {
                $q->where('parent_id', $user->id);
            });
        }

        // 🔎 Apply search
        if ($search = $req->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('accounts', function ($qa) use ($search) {
                      $qa->where('serial_number', 'like', "%{$search}%");
                  });
            });
        }

        // ✅ Finalize query (same for admin + non-admin)
        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('dashboard', compact('user','accounts','users'));
    }
}
