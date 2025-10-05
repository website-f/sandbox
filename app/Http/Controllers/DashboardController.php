<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    public function index(Request $req) {
    $user = $req->user();

    $accounts = $user->accounts()->with('accountType')->get()->keyBy('account_type_id');
    
    $subscriptions = $user->subscriptions()
        ->with(['payment', 'accountType'])
        ->latest()
        ->get()
        ->groupBy('account_type_id');

    // Admin Stats
    $stats = null;
    if ($user->hasRole('Admin')) {
        $stats = [
            'total_users' => User::count(),
            'total_rizqmall' => DB::table('accounts')->where('type', 'rizqmall')->where('active', 1)->count(),
            'total_sandbox' => \DB::table('accounts')->where('type', 'sandbox')->where('active', 1)->count(),
            'total_profit' => \DB::table('subscriptions')->where('status', 'paid')->sum('amount') / 100, // Convert cents to RM
            'total_subscriptions' => \DB::table('subscriptions')->count(),
        ];
    }

    // Start query
    $query = User::with(['accounts', 'profile', 'referral.parent', 'referral']);

    if (!$user->hasRole('Admin')) {
        // Non-admin: only see their own downline
        $query->whereHas('referral', function ($q) use ($user) {
            $q->where('parent_id', $user->id);
        });
    }

    // Apply search
    if ($search = $req->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhereHas('accounts', function ($qa) use ($search) {
                  $qa->where('serial_number', 'like', "%{$search}%");
              });
        });
    }

    $users = $query->orderBy('name')->paginate(10)->withQueryString();

    return view('dashboard', compact('user','accounts','users', 'subscriptions', 'stats'));
}

// New method for user location breakdown
public function getUsersByLocation(Request $req)
{
    $country = $req->input('country');
    $state = $req->input('state');
    
    $query = \DB::table('users')
        ->join('profiles', 'users.id', '=', 'profiles.user_id')
        ->select('users.id', 'users.name', 'users.email', 'profiles.country', 'profiles.state', 'profiles.city');
    
    if ($country) {
        $query->where('profiles.country', $country);
    }
    
    if ($state) {
        $query->where('profiles.state', $state);
    }
    
    $users = $query->get();
    
    // Get unique countries, states, cities for filters
    $countries = \DB::table('profiles')->whereNotNull('country')->distinct()->pluck('country');
    $states = \DB::table('profiles')->whereNotNull('state')->distinct()->pluck('state');
    $cities = \DB::table('profiles')->whereNotNull('city')->distinct()->pluck('city');
    
    return response()->json([
        'users' => $users,
        'count' => $users->count(),
        'countries' => $countries,
        'states' => $states,
        'cities' => $cities
    ]);
}
}
