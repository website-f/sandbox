<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\Blacklist;
use App\Models\Collection;
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
    // Run deletion + creation in a transaction to avoid partial state
    DB::transaction(function () use ($user) {
        // 1) Remove any malformed/empty type rows for this user (NULL, empty or whitespace-only)
        Collection::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNull('type')
                  ->orWhereRaw("TRIM(COALESCE(type, '')) = ''");
            })
            ->delete();

        // 2) Ensure the 3 default collections exist
        $defaultTypes = ['geran_asas', 'tabung_usahawan', 'had_pembiayaan'];

        foreach ($defaultTypes as $type) {
            Collection::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $type,
                ],
                [
                    'balance' => 0,
                    'pending_balance' => 0,
                    'limit' => null,
                    'is_redeemed' => 0,
                ]
            );
        }
    });

    // 3) Eager load and rest of your logic (unchanged)
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
        'referrals',
    ]);

    $walletTransactions = $user->wallet?->transactions()->latest()->limit(50)->get() ?? collect();
    $payments = \App\Models\Payment::whereHas('subscription', fn($q) => $q->where('user_id', $user->id))
        ->latest()->limit(50)->get();

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

public function updateProfile(Request $request, User $user)
{
    $validated = $request->validate([
        'full_name' => 'nullable|string|max:255',
        'nric' => 'nullable|string|max:50',
        'dob' => 'nullable|date',
        'phone' => 'nullable|string|max:20',
        'home_address' => 'nullable|string|max:500',
        'country' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'city' => 'nullable|string|max:100',
    ]);

    // Create profile if it doesn't exist
    if (!$user->profile) {
        $user->profile()->create($validated);
    } else {
        $user->profile->update($validated);
    }

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'profile' => $user->profile->fresh()
    ]);
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


public function sandboxReferralTree(User $user) {
    // Get all active sandbox accounts user_ids
    $activeSandboxUserIds = \DB::table('accounts')
        ->where('type', 'sandbox')
        ->where('active', 1)
        ->pluck('user_id')
        ->toArray();

    if (empty($activeSandboxUserIds)) {
        return response()->json(['tree' => []]);
    }

    $ref = $user->referral;
    if (!$ref) {
        return response()->json(['tree' => []]);
    }

    // Build filtered tree
    $tree = $this->buildFilteredSandboxTree($ref->id, $activeSandboxUserIds);
    
    if (!$tree) {
        return response()->json(['tree' => []]);
    }

    // Limit top-level children to 10
    if (isset($tree['children']) && is_array($tree['children'])) {
        $tree['children'] = array_slice($tree['children'], 0, 10);
    }

    return response()->json(['tree' => $tree]);
}

/** helper: build sandbox referral tree - only show users with active sandbox accounts */
protected function buildFilteredSandboxTree($referralId, $activeSandboxUserIds) {
    $ref = \App\Models\Referral::with('user')->find($referralId);
    if (!$ref) return null;

    // Check if this user has an active sandbox account
    $hasActiveSandbox = in_array($ref->user_id, $activeSandboxUserIds);
    
    // Get all children
    $children = \App\Models\Referral::where('parent_id', $ref->user_id)->get();
    
    // Recursively build children that have active sandbox accounts
    $filteredChildren = [];
    foreach ($children as $childRef) {
        $childNode = $this->buildFilteredSandboxTree($childRef->id, $activeSandboxUserIds);
        if ($childNode) {
            $filteredChildren[] = $childNode;
        }
    }
    
    // If this user doesn't have active sandbox AND has no children with sandbox, skip this node
    if (!$hasActiveSandbox && empty($filteredChildren)) {
        return null;
    }

    // Build the node
    $node = [
        'id'       => $ref->id,
        'user_id'  => $ref->user_id,
        'name'     => $ref->user->name ?? $ref->user_id,
        'children' => $filteredChildren
    ];

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

public function updateName(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
    ]);

    if ($user->profile) {
        $user->profile->full_name = $validated['name'];
        $user->profile->save();
    } else {
        $user->name = $validated['name'];
        $user->save();
    }

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'name' => $validated['name'],
        ]);
    }

    return redirect()->back()->with('success', 'User name updated successfully.');
}

public function updatePhone(Request $request, User $user)
{
    $validated = $request->validate([
        'phone' => 'required',
    ]);

    
    $user->profile->phone = $validated['phone'];
    $user->profile->save();

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'phone' => $validated['phone'],
        ]);
    }

    return redirect()->back()->with('success', 'User Phone updated successfully.');
}

public function syncSandboxRewards(User $user)
{
    try {
        // Get all active sandbox accounts user_ids
        $activeSandboxUserIds = \DB::table('accounts')
            ->where('type', 'sandbox')
            ->where('active', 1)
            ->pluck('user_id')
            ->toArray();

        if (empty($activeSandboxUserIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No active sandbox accounts found'
            ]);
        }

        // Count direct children with active sandbox under this user
        $directSandboxCount = \App\Models\Referral::where('parent_id', $user->id)
            ->whereIn('user_id', $activeSandboxUserIds)
            ->count();

        // Ensure all collections exist
        $collections = $this->ensureCollections($user);
        $geranAsas = $collections['geran_asas'];
        $tabungUsahawan = $collections['tabung_usahawan'];
        $hadPembiayaan = $collections['had_pembiayaan'];

        // SYNC GERAN ASAS (6000 per referral, max 10)
        if ($directSandboxCount < 10) {
            // Still collecting - amount goes to pending
            $expectedPending = $directSandboxCount * 6000;
            $expectedBalance = 0;
        } else {
            // Completed (10 or more referrals) - moves to balance
            $expectedPending = 0;
            $expectedBalance = 60000; // 10 * 6000
        }
        
        // Update Geran Asas balances
        if ($geranAsas->pending_balance != $expectedPending || $geranAsas->balance != $expectedBalance) {
            $geranAsas->pending_balance = $expectedPending;
            $geranAsas->balance = $expectedBalance;
            $geranAsas->save();
            
            // Log transaction if completed
            if ($expectedBalance > 0 && $directSandboxCount >= 10) {
                $geranAsas->transactions()->create([
                    'type' => 'credit',
                    'amount' => 60000,
                    'description' => "Geran Asas completed (10 referrals) - Sync adjustment",
                ]);
            }
        }

        // SYNC TABUNG USAHAWAN & HAD PEMBIAYAAN
        // Each referral gives 2000 split (1000 each)
        $expectedPerCollection = $directSandboxCount * 1000;
        
        // Update Tabung Usahawan
        if ($tabungUsahawan->balance != $expectedPerCollection) {
            $difference = $expectedPerCollection - $tabungUsahawan->balance;
            if ($difference > 0 && $tabungUsahawan->balance + $difference <= 50000000) {
                $tabungUsahawan->balance = $expectedPerCollection;
                $tabungUsahawan->save();
                
                $tabungUsahawan->transactions()->create([
                    'type' => 'credit',
                    'amount' => $difference,
                    'description' => "Sync adjustment: {$directSandboxCount} sandbox referrals",
                ]);
            }
        }

        // Update Had Pembiayaan
        if ($hadPembiayaan->balance != $expectedPerCollection) {
            $difference = $expectedPerCollection - $hadPembiayaan->balance;
            if ($difference > 0 && $hadPembiayaan->balance + $difference <= 50000000) {
                $hadPembiayaan->balance = $expectedPerCollection;
                $hadPembiayaan->save();
                
                $hadPembiayaan->transactions()->create([
                    'type' => 'credit',
                    'amount' => $difference,
                    'description' => "Sync adjustment: {$directSandboxCount} sandbox referrals",
                ]);
            }
        }

        // SYNC UPLINE REWARDS (recursive)
        $this->syncUplineRewards($user, $activeSandboxUserIds);

        // Response message
        $message = "Synced! {$directSandboxCount} active sandbox referrals found.\n";
        $message .= "- Geran Asas: RM " . number_format($geranAsas->balance / 100, 2) . " (balance), RM " . number_format($geranAsas->pending_balance / 100, 2) . " (pending)\n";
        $message .= "- Tabung Usahawan: RM " . number_format($tabungUsahawan->balance / 100, 2) . "\n";
        $message .= "- Had Pembiayaan: RM " . number_format($hadPembiayaan->balance / 100, 2);

        return response()->json([
            'success' => true,
            'message' => $message,
            'direct_count' => $directSandboxCount,
            'geran_pending' => $geranAsas->pending_balance,
            'geran_balance' => $geranAsas->balance,
            'tabung_usahawan' => $tabungUsahawan->balance,
            'had_pembiayaan' => $hadPembiayaan->balance
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Sync failed: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Sync upline rewards recursively through the tree
 */
private function syncUplineRewards(User $user, array $activeSandboxUserIds)
{
    // Get upline (referrer)
    $upline = $user->referrer ?? null;
    if (!$upline) return;

    // Count how many users under current user have active sandbox
    $childrenCount = \App\Models\Referral::where('parent_id', $user->id)
        ->whereIn('user_id', $activeSandboxUserIds)
        ->count();

    if ($childrenCount == 0) return;

    // Ensure upline collections
    $uplineCollections = $this->ensureCollections($upline);

    // Upline gets 2000 (1000 each) per referral of their downline
    $expectedPerCollection = $childrenCount * 1000;

    // Update Tabung Usahawan
    $tabungUsahawan = $uplineCollections['tabung_usahawan'];
    if ($tabungUsahawan->balance < $expectedPerCollection) {
        $difference = $expectedPerCollection - $tabungUsahawan->balance;
        if ($tabungUsahawan->balance + $difference <= 50000000) {
            $tabungUsahawan->balance += $difference;
            $tabungUsahawan->save();
            
            $tabungUsahawan->transactions()->create([
                'type' => 'credit',
                'amount' => $difference,
                'description' => "Upline sync: {$childrenCount} referrals from {$user->name}",
            ]);
        }
    }

    // Update Had Pembiayaan
    $hadPembiayaan = $uplineCollections['had_pembiayaan'];
    if ($hadPembiayaan->balance < $expectedPerCollection) {
        $difference = $expectedPerCollection - $hadPembiayaan->balance;
        if ($hadPembiayaan->balance + $difference <= 50000000) {
            $hadPembiayaan->balance += $difference;
            $hadPembiayaan->save();
            
            $hadPembiayaan->transactions()->create([
                'type' => 'credit',
                'amount' => $difference,
                'description' => "Upline sync: {$childrenCount} referrals from {$user->name}",
            ]);
        }
    }

    // Continue up the chain
    $this->syncUplineRewards($upline, $activeSandboxUserIds);
}

/**
 * Ensure all 3 collections exist
 */
private function ensureCollections(User $u): array
{
    return [
        'geran_asas' => \App\Models\Collection::firstOrCreate(
            ['user_id' => $u->id, 'type' => 'geran_asas'],
            ['balance' => 0, 'pending_balance' => 0, 'limit' => 60000]
        ),
        'tabung_usahawan' => \App\Models\Collection::firstOrCreate(
            ['user_id' => $u->id, 'type' => 'tabung_usahawan'],
            ['balance' => 0, 'limit' => 50000000]
        ),
        'had_pembiayaan' => \App\Models\Collection::firstOrCreate(
            ['user_id' => $u->id, 'type' => 'had_pembiayaan'],
            ['balance' => 0, 'limit' => 50000000]
        ),
    ];
}
    
}
