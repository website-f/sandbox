<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use App\Models\Referral;
use App\Models\Blacklist;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function create()
    {
        return view('admin.user-create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'nullable|min:6',
            'rizqmall_start' => 'nullable|date',
    
            // validate manual serials if provided
            'rizqmall_serial' => 'nullable|string|unique:accounts,serial_number',
            'sandbox_serial' => 'nullable|string|unique:accounts,serial_number',
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password ?? 'password123'),
        ]);
    
        // Referral
        $refCode = $this->generateUniqueRefCode();
        Referral::create([
            'user_id' => $user->id,
            'ref_code' => $refCode,
            'level' => 1,
        ]);
    
        // Rizqmall
        if ($request->rizqmall_active) {
            $serial = $request->rizqmall_serial_mode === 'manual'
                ? $request->rizqmall_serial
                : $this->generateUniqueSerial('rizqmall');
    
            $start = Carbon::parse($request->rizqmall_start);
            $expires = $start->copy()->addYear();
    
            Account::create([
                'user_id' => $user->id,
                'type' => 'rizqmall',
                'active' => true,
                'serial_number' => $serial,
                'expires_at' => $expires,
            ]);
        }
    
        // Sandbox
        if ($request->sandbox_active) {
            $serial = $request->sandbox_serial_mode === 'manual'
                ? $request->sandbox_serial
                : $this->generateUniqueSerial('sandbox');
    
            Account::create([
                'user_id' => $user->id,
                'type' => 'sandbox',
                'active' => true,
                'serial_number' => $serial,
            ]);
        }
    
        return redirect()->route('admin.users.index')
            ->with('status', "User {$user->name} created.");
    }

    
    protected function generateUniqueRefCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Referral::where('ref_code', $code)->exists());
    
        return $code;
    }
    
    protected function generateUniqueSerial($type)
    {
        do {
            $serial = Account::generateSerial($type);
        } while (Account::where('serial_number', $serial)->exists());
    
        return $serial;
    }

    public function checkSerial(Request $request)
{
    $request->validate([
        'serial' => 'required|string',
    ]);

    $exists = Account::where('serial_number', $request->serial)->exists();

    return response()->json([
        'exists' => $exists
    ]);
}

// app/Http/Controllers/UserController.php
public function checkEmail(Request $request)
{
    $exists = \App\Models\User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
}

/**
 * Show full user detail page
 */
public function show(User $user)
{
    // eager load everything useful
    $user->load([
        'profile',
        'business',
        'bank',
        'education',
        'collections',
        'courses',
        'nextOfKin',
        'affiliations',
        'accounts',
        'wallet',
        'wallet.transactions',
        'subscriptions.payment',
        // referral relations
        'referrals',
    ]);

    // build small summary for wallet and payments (latest 20 items)
    $walletTransactions = $user->wallet?->transactions()->latest()->limit(50)->get() ?? collect();
    $payments = \App\Models\Payment::whereHas('subscription', fn($q)=>$q->where('user_id', $user->id))->latest()->limit(50)->get();

    return view('admin.user-show', compact('user', 'walletTransactions', 'payments'));
}

/**
 * Toggle admin via AJAX (also still allow old toggleAdmin for form)
 */
public function toggleAdminAjax(Request $request, User $user)
{
    // authorize: allow only Admins to do this
    if (!auth()->user()->can('manage users')) {
        abort(403);
    }

    if ($user->hasRole('Admin')) {
        $user->removeRole('Admin');
        $status = 'removed';
    } else {
        $user->assignRole('Admin');
        $status = 'assigned';
    }

    return response()->json(['ok' => true, 'status' => $status, 'is_admin' => $user->hasRole('Admin')]);
}

/**
 * Toggle account active/inactive (account is route-model bound)
 */
public function toggleAccountActive(Request $request, User $user, Account $account)
{
    if ($account->user_id !== $user->id) abort(404);
    if (!auth()->user()->can('manage users')) abort(403);

    $account->active = ! $account->active;
    $account->save();

    return response()->json(['ok' => true, 'active' => $account->active]);
}

/**
 * Update account serial (manual edit)
 */
public function updateAccountSerial(Request $request, User $user, Account $account)
{
    $request->validate([
        'serial_number' => 'required|string|max:255|unique:accounts,serial_number,' . $account->id,
    ]);

    if ($account->user_id !== $user->id) abort(404);
    if (!auth()->user()->can('manage users')) abort(403);

    $account->serial_number = $request->serial_number;
    $account->save();

    return response()->json(['ok' => true, 'serial' => $account->serial_number]);
}

public function referralTree(User $user)
{
    $ref = $user->referral;
    if (!$ref) {
        return response()->json(['tree' => []]);
    }

    // build full tree first
    $tree = $this->buildReferralTree($ref->id);

    // limit top-level children to 10
    if (isset($tree['children']) && is_array($tree['children'])) {
        $tree['children'] = array_slice($tree['children'], 0, 10);
    }

    return response()->json(['tree' => $tree]);
}


/**
 * For sandbox_referrals (separate table)
 */
public function sandboxReferralTree(User $user)
{
    // Try find sandbox referral row directly for this user
    $root = \DB::table('sandbox_referrals')->where('user_id', $user->id)->first();

    // If none, check if this user is a parent of any sandbox_referrals
    if (!$root) {
        $child = \DB::table('sandbox_referrals')->where('parent_id', $user->id)->first();
        if ($child) {
            // fake root node for this user
            $root = (object)[
                'id'      => "u{$user->id}", // avoid collision with real ids
                'user_id' => $user->id,
                'parent_id' => null,
                'root_id' => null,
            ];
        }
    }

    if (!$root) {
        return response()->json(['tree' => []]);
    }

    $all = \DB::table('sandbox_referrals')->get()->keyBy('id')->toArray();

    $node = $this->buildSandboxTree($all, $root->id, $root->user_id);
    return response()->json(['tree' => $node]);
}

/** helper: build referral tree from referrals table (Eloquent) */
protected function buildReferralTree($referralId)
{
    $ref = \App\Models\Referral::with('user')->find($referralId);
    if (!$ref) return null;

    $node = [
        'id'      => $ref->id,
        'user_id' => $ref->user_id,
        'name'    => $ref->user->name ?? $ref->user_id,
        'children'=> []
    ];

    // FIX: children match parent_id (user_id of current node)
    $children = \App\Models\Referral::where('parent_id', $ref->user_id)->get();

    foreach ($children as $childRef) {
        $node['children'][] = $this->buildReferralTree($childRef->id);
    }

    return $node;
}


/** helper for sandbox_referrals tree (array of objects keyed by id) */
protected function buildSandboxTree($all, $referralId, $fallbackUserId = null)
{
    if (is_string($referralId) && str_starts_with($referralId, 'u')) {
        // fake root case
        $userId = $fallbackUserId;
        $node = [
            'id'      => $referralId,
            'user_id' => $userId,
            'name'    => \App\Models\User::find($userId)?->name ?? $userId,
            'children'=> []
        ];

        // children = rows where parent_id == this user_id
        $children = collect($all)->where('parent_id', $userId);
        foreach ($children as $child) {
            $node['children'][] = $this->buildSandboxTree($all, $child->id);
        }

        return $node;
    }

    $self = $all[$referralId] ?? null;
    if (!$self) return null;

    $node = [
        'id'      => $self->id,
        'user_id' => $self->user_id,
        'name'    => \App\Models\User::find($self->user_id)?->name ?? $self->user_id,
        'children'=> []
    ];

    $children = collect($all)->where('parent_id', $self->id);
    foreach ($children as $child) {
        $node['children'][] = $this->buildSandboxTree($all, $child->id);
    }

    return $node;
}


public function edit(User $user)
{
    $user->load(['profile', 'accounts', 'referrals']);
    return view('admin.user-edit', compact('user'));
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
    ]);

    $user->update($request->only('name', 'email'));

    return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
}

public function blacklists()
{
    
    return view('admin.blacklist.index');
}

public function addToBlacklist(Request $request, User $user)
{
    // check if already blacklisted
    if(Blacklist::where('email', $user->email)->exists()){
        return response()->json(['ok' => false, 'error' => 'User already blacklisted']);
    }

    Blacklist::create([
        'email' => $user->email,
        'reason' => 'Admin added', 
        'name'  => $user->name,    
        'phone' => $user->profile?->phone ?? null, 
    ]);

    // return JSON for AJAX success
    return response()->json(['ok' => true]);
}

public function destroy(User $user)
{
    try {
        // Remove this user from being parent/root in referrals
        DB::table('referrals')
            ->where('parent_id', $user->id)
            ->orWhere('root_id', $user->id)
            ->update(['parent_id' => null, 'root_id' => null]);

        // Delete the user (cascade will remove profiles, businesses, educations, accounts, subscriptions, payments, etc.)
        $user->delete();

        return response()->json(['ok' => true]);
    } catch (\Exception $e) {
        return response()->json(['ok' => false, 'error' => 'Failed to delete user: ' . $e->getMessage()]);
    }
}


public function redeemCollection(Request $request, $userId, $type)
    {
        $user = User::findOrFail($userId);

        $collection = $user->collections()->where('type', $type)->firstOrFail();

        // dd($userId, $type);

        // if ($collection->balance < $collection->limit) {
        //     return redirect()->back()->with('error', 'Balance has not reached the limit yet.');
        // }

        $collection->is_redeemed = true;
        $collection->save();

        return redirect()->back()->with('success', "Collection '{$collection->type}' marked as redeemed.");
    }


    
}
