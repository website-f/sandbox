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
use App\Models\CollectionType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRoleController extends Controller
{
    private const LEGACY_SANDBOX_ACCOUNT_TYPES = [
        'sandbox',
        'sandbox remaja',
        'sandbox usahawan',
        'sandbox awam',
    ];

    private const COLLECTION_CODES_BY_SUBTYPE = [
        Account::SUBTYPE_USAHAWAN => ['geran_asas', 'tabung_usahawan', 'had_pembiayaan'],
        Account::SUBTYPE_REMAJA => ['biasiswa_pemula', 'had_biasiswa', 'dana_usahawan_muda'],
        Account::SUBTYPE_AWAM => ['modal_pemula', 'had_pembiayaan_hutang', 'khairat_kematian'],
    ];

    private const COLLECTION_LABELS = [
        'geran_asas' => 'Geran Asas',
        'tabung_usahawan' => 'Tabung Usahawan',
        'had_pembiayaan' => 'Had Pembiayaan',
        'biasiswa_pemula' => 'Biasiswa Pemula',
        'had_biasiswa' => 'Had Biasiswa',
        'dana_usahawan_muda' => 'Dana Usahawan Muda',
        'modal_pemula' => 'Modal Pemula',
        'had_pembiayaan_hutang' => 'Had Pembiayaan Hutang',
        'khairat_kematian' => 'Khairat Kematian',
    ];

    public function index(Request $request)
    {
        // Load all users for DataTables client-side processing
        // DataTables will handle pagination, sorting, and searching
        $users = User::with(['roles', 'accounts', 'profile', 'referral.parent'])
            ->orderBy('name')
            ->get();

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

    protected function generateUniqueSerial($type, $subtype = null)
    {
        do {
            $serial = Account::generateSerial($type, $subtype);
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
        $expectedCollectionCodes = $this->getCollectionCodesForUser($user);

        // Run deletion + creation in a transaction to avoid partial state
        DB::transaction(function () use ($user) {
            // 1) Remove any malformed/empty type rows for this user (NULL, empty or whitespace-only)
            Collection::where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereNull('type')
                        ->orWhereRaw("TRIM(COALESCE(type, '')) = ''");
                })
                ->delete();

            // 2) Ensure the user's own sandbox-specific collections exist
            Collection::createForUser($user->id, $user->getCollectionAccountType());
        });

        // 3) Eager load and rest of your logic (unchanged)
        $user->load([
            'profile',
            'business',
            'bank',
            'education',
            'collections',
            'collections.transactions.creator',
            'courses',
            'nextOfKin',
            'pewaris.linkedUser.accounts',
            'affiliations',
            'accounts',
            'wallet',
            'wallet.transactions',
            'subscriptions.payment',
            'referrals',
        ]);

        $displayCollections = $user->collections
            ->filter(fn($collection) => in_array($collection->type, $expectedCollectionCodes, true))
            ->sortBy(function ($collection) use ($expectedCollectionCodes) {
                $position = array_search($collection->type, $expectedCollectionCodes, true);
                return $position === false ? 999 : $position;
            })
            ->values();

        $availableCollectionTypes = $displayCollections
            ->map(function ($collection) {
                return [
                    'code' => $collection->type,
                    'name' => $collection->display_name,
                ];
            })
            ->values();

        if ($availableCollectionTypes->isEmpty()) {
            $availableCollectionTypes = collect($expectedCollectionCodes)
                ->map(fn($code) => [
                    'code' => $code,
                    'name' => $this->getCollectionDisplayName($code),
                ])
                ->values();
        }

        $walletTransactions = $user->wallet?->transactions()->latest()->limit(50)->get() ?? collect();
        $payments = \App\Models\Payment::whereHas('subscription', fn($q) => $q->where('user_id', $user->id))
            ->latest()->limit(50)->get();

        return view('admin.user-show', compact(
            'user',
            'walletTransactions',
            'payments',
            'displayCollections',
            'availableCollectionTypes'
        ));
    }

    /**
     * Toggle admin via AJAX (also still allow old toggleAdmin for form)
     */
    public function toggleAdminAjax(Request $request, User $user)
    {
        // authorize: allow only Admins to do this
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        if ($user->hasRole('Admin')) {
            $user->removeRole('Admin');
            $status = 'removed';
        } else {
            $user->assignRole('Admin');
            $status = 'assigned';
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'status' => $status, 'is_admin' => $user->hasRole('Admin')]);
        }
        return back()->with('status', "Admin role toggled.");
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
     * When activating, generates a unique serial number if one doesn't exist
     */
    public function toggleAccountActive(Request $request, User $user, Account $account)
    {
        if ($account->user_id !== $user->id) abort(404);
        if (!auth()->user()->hasRole('Admin')) abort(403);

        $account->active = !$account->active;

        // If activating and no serial number exists, generate one
        if ($account->active && empty($account->serial_number)) {
            $account->serial_number = Account::generateUniqueSerial($account->type, $account->subtype);

            // For rizqmall accounts, also set expiry date (1 year from now)
            if ($account->type === 'rizqmall' && !$account->expires_at) {
                $account->expires_at = Carbon::now()->addYear();
            }
        }

        $account->save();


        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'active' => $account->active,
                'serial' => $account->serial_number,
                'expires_at' => $account->expires_at?->format('d M Y')
            ]);
        }

        return back()->with('success', 'Account status updated.');
    }

    /**
     * Update account serial (manual edit)
     */
    public function updateAccountSerial(Request $request, User $user, Account $account)
    {
        if ($account->user_id !== $user->id) {
            return response()->json(['ok' => false, 'error' => 'Account not found'], 404);
        }

        if (!auth()->user()->hasRole('Admin')) {
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }

        $validator = \Validator::make($request->all(), [
            'serial_number' => 'required|string|max:255|unique:accounts,serial_number,' . $account->id,
        ], [
            'serial_number.required' => 'Serial number is required',
            'serial_number.unique' => 'This serial number is already assigned to another account',
            'serial_number.max' => 'Serial number must not exceed 255 characters',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'error' => $validator->errors()->first('serial_number')
            ], 422);
        }

        $account->serial_number = $request->serial_number;
        $account->save();

        return response()->json(['ok' => true, 'serial' => $account->serial_number]);
    }

    /**
     * Create a new account for user (RizqMall or Sandbox)
     */
    public function createAccount(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('Admin')) {
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }

        $validator = \Validator::make($request->all(), [
            'type' => 'required|in:rizqmall,sandbox',
        ]);

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'error' => $validator->errors()->first()], 422);
        }

        $type = $request->type;
        $sandboxSubtype = $this->normalizeSandboxSubtype($user->getSandboxSubtype());

        // Check if account already exists
        $existingAccountQuery = Account::where('user_id', $user->id);
        if ($type === Account::TYPE_SANDBOX) {
            $existingAccountQuery->whereIn('type', self::LEGACY_SANDBOX_ACCOUNT_TYPES);
        } else {
            $existingAccountQuery->where('type', $type);
        }

        $existingAccount = $existingAccountQuery->first();

        if ($existingAccount) {
            return response()->json([
                'ok' => false,
                'error' => ucfirst($type) . ' account already exists for this user'
            ], 422);
        }

        $accountTypeId = null;
        if ($type === Account::TYPE_SANDBOX) {
            $accountTypeId = \App\Models\AccountType::where('name', 'sandbox_' . $sandboxSubtype)->value('id')
                ?? \App\Models\AccountType::where('name', 'sandbox')->value('id');
        } elseif ($type === Account::TYPE_RIZQMALL) {
            $accountTypeId = \App\Models\AccountType::where('name', 'rizqmall')->value('id');
        }

        // Create new account
        $account = Account::create([
            'user_id' => $user->id,
            'type' => $type,
            'subtype' => $type === Account::TYPE_SANDBOX ? $sandboxSubtype : null,
            'account_type_id' => $accountTypeId,
            'active' => false,
            'serial_number' => null,
            'expires_at' => null,
        ]);


        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => ucfirst($type) . ' account created successfully',
                'account' => [
                    'id' => $account->id,
                    'type' => $account->type,
                    'active' => $account->active,
                    'serial_number' => $account->serial_number,
                    'expires_at' => $account->expires_at?->format('d M Y'),
                ]
            ]);
        }

        return back()->with('success', ucfirst($type) . ' account created successfully.');
    }

    /**
     * Check which accounts exist for a user
     */
    public function checkAccounts(User $user)
    {
        if (!auth()->user()->hasRole('Admin')) {
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
        }

        $accounts = $user->accounts()->get(['id', 'type', 'active', 'serial_number', 'expires_at']);

        $hasRizqmall = $accounts->where('type', 'rizqmall')->isNotEmpty();
        $hasSandbox = $accounts->contains(fn($account) => $this->isSandboxAccountType($account->type));

        return response()->json([
            'ok' => true,
            'has_rizqmall' => $hasRizqmall,
            'has_sandbox' => $hasSandbox,
            'accounts' => $accounts,
        ]);
    }

    public function referralTree(User $user)
    {
        $ref = $user->referral;
        if (!$ref) {
            return response()->json(['tree' => []]);
        }

        // build full tree first
        $visited = [];
        $tree = $this->buildReferralTree($ref->id, $visited);

        // limit top-level children to 10
        if (isset($tree['children']) && is_array($tree['children'])) {
            $tree['children'] = array_slice($tree['children'], 0, 10);
        }

        return response()->json(['tree' => $tree]);
    }

    /** helper: build referral tree from referrals table (Eloquent) */
    protected function buildReferralTree($referralId, array &$visited = [])
    {
        if (isset($visited[$referralId])) {
            return null;
        }
        $visited[$referralId] = true;

        $ref = \App\Models\Referral::with('user')->find($referralId);
        if (!$ref) {
            return null;
        }

        $node = [
            'id'      => $ref->id,
            'user_id' => $ref->user_id,
            'name'    => $ref->user->name ?? $ref->user_id,
            'children' => []
        ];

        // FIX: children match parent_id (user_id of current node)
        $children = \App\Models\Referral::where('parent_id', $ref->user_id)->get();

        foreach ($children as $childRef) {
            $childNode = $this->buildReferralTree($childRef->id, $visited);
            if ($childNode) {
                $node['children'][] = $childNode;
            }
        }

        return $node;
    }


    public function sandboxReferralTree(User $user)
    {
        // Get all active sandbox accounts user_ids
        $activeSandboxUserIds = $this->getActiveSandboxUserIds();

        if (empty($activeSandboxUserIds)) {
            return response()->json(['tree' => []]);
        }

        $ref = $user->referral;
        if (!$ref) {
            return response()->json(['tree' => []]);
        }

        // Build filtered tree
        $visited = [];
        $tree = $this->buildFilteredSandboxTree($ref->id, $activeSandboxUserIds, $visited);

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
    protected function buildFilteredSandboxTree($referralId, $activeSandboxUserIds, array &$visited = [])
    {
        if (isset($visited[$referralId])) {
            return null;
        }
        $visited[$referralId] = true;

        $ref = \App\Models\Referral::with('user')->find($referralId);
        if (!$ref) {
            return null;
        }

        // Check if this user has an active sandbox account
        $hasActiveSandbox = in_array($ref->user_id, $activeSandboxUserIds);

        // Get all children
        $children = \App\Models\Referral::where('parent_id', $ref->user_id)->get();

        // Recursively build children that have active sandbox accounts
        $filteredChildren = [];
        foreach ($children as $childRef) {
            $childNode = $this->buildFilteredSandboxTree($childRef->id, $activeSandboxUserIds, $visited);
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

    /**
     * Reset user password to default "password123"
     */
    public function resetPassword(Request $request, User $user)
    {
        if (!auth()->user()->hasRole('Admin')) {
            if ($request->wantsJson()) {
                return response()->json(['ok' => false, 'error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $user->update([
            'password' => Hash::make('password123'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => "Password for {$user->name} has been reset to 'password123'",
            ]);
        }

        return back()->with('success', "Password for {$user->name} has been reset to 'password123'");
    }

    public function blacklists()
    {
        $blacklists = Blacklist::latest()->get();
        return view('admin.blacklist.index', compact('blacklists'));
    }

    public function addToBlacklist(Request $request, User $user)
    {
        // check if already blacklisted
        if (Blacklist::where('email', $user->email)->exists()) {
            return response()->json(['ok' => false, 'error' => 'User already blacklisted']);
        }

        Blacklist::create([
            'email' => $user->email,
            'reason' => 'Admin added',
            'name'  => $user->name,
            'phone' => $user->profile?->phone ?? null,
        ]);

        // return JSON for AJAX success
        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'User added to blacklist.');
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
            $activeSandboxUserIds = $this->getActiveSandboxUserIds();

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
            $collectionCodes = $this->getCollectionCodesForUser($user);
            $starterType = $collectionCodes[0] ?? null;
            $secondaryTypes = array_slice($collectionCodes, 1, 2);

            if (!$starterType || count($secondaryTypes) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Collection mapping is incomplete for this sandbox subtype.'
                ], 422);
            }

            $starterCollection = $collections[$starterType] ?? null;
            $secondaryAType = $secondaryTypes[0];
            $secondaryBType = $secondaryTypes[1];
            $secondaryA = $collections[$secondaryAType] ?? null;
            $secondaryB = $collections[$secondaryBType] ?? null;

            if (!$starterCollection || !$secondaryA || !$secondaryB) {
                return response()->json([
                    'success' => false,
                    'message' => 'Required collections are missing for this user.'
                ], 422);
            }

            // Starter collection (6000 per referral, max 10)
            if ($directSandboxCount < 10) {
                // Still collecting - amount goes to pending
                $expectedPending = $directSandboxCount * 6000;
                $expectedBalance = 0;
            } else {
                // Completed (10 or more referrals) - moves to balance
                $expectedPending = 0;
                $expectedBalance = 60000; // 10 * 6000
            }

            // Update starter collection balances
            if ($starterCollection->pending_balance != $expectedPending || $starterCollection->balance != $expectedBalance) {
                $starterCollection->pending_balance = $expectedPending;
                $starterCollection->balance = $expectedBalance;
                $starterCollection->save();

                // Log transaction if completed
                if ($expectedBalance > 0 && $directSandboxCount >= 10) {
                    $starterCollection->transactions()->create([
                        'type' => 'credit',
                        'amount' => 60000,
                        'description' => $this->getCollectionDisplayName($starterType) . " completed (10 referrals) - Sync adjustment",
                    ]);
                }
            }

            // Sync secondary collections
            // Each referral gives 2000 split (1000 each)
            $expectedPerCollection = $directSandboxCount * 1000;

            foreach ([$secondaryAType => $secondaryA, $secondaryBType => $secondaryB] as $type => $collection) {
                if ($collection->balance != $expectedPerCollection) {
                    $difference = $expectedPerCollection - $collection->balance;
                    if ($difference > 0 && $collection->balance + $difference <= 50000000) {
                        $collection->balance = $expectedPerCollection;
                        $collection->save();

                        $collection->transactions()->create([
                            'type' => 'credit',
                            'amount' => $difference,
                            'description' => "Sync adjustment: {$directSandboxCount} sandbox referrals",
                        ]);
                    }
                }
            }

            // SYNC UPLINE REWARDS (recursive)
            $this->syncUplineRewards($user, $activeSandboxUserIds);

            // Response message
            $message = "Synced! {$directSandboxCount} active sandbox referrals found.\n";
            $message .= "- " . $this->getCollectionDisplayName($starterType) . ": RM " . number_format($starterCollection->balance / 100, 2)
                . " (balance), RM " . number_format($starterCollection->pending_balance / 100, 2) . " (pending)\n";
            $message .= "- " . $this->getCollectionDisplayName($secondaryAType) . ": RM " . number_format($secondaryA->balance / 100, 2) . "\n";
            $message .= "- " . $this->getCollectionDisplayName($secondaryBType) . ": RM " . number_format($secondaryB->balance / 100, 2);

            return response()->json([
                'success' => true,
                'message' => $message,
                'direct_count' => $directSandboxCount,
                'starter_pending' => $starterCollection->pending_balance,
                'starter_balance' => $starterCollection->balance,
                $secondaryAType => $secondaryA->balance,
                $secondaryBType => $secondaryB->balance,
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
        $uplineCollectionCodes = $this->getCollectionCodesForUser($upline);
        $uplineSecondaryTypes = array_slice($uplineCollectionCodes, 1, 2);
        if (count($uplineSecondaryTypes) < 2) {
            return;
        }

        // Upline gets 2000 (1000 each) per referral of their downline
        $expectedPerCollection = $childrenCount * 1000;

        // Update both secondary collections according to upline sandbox subtype
        foreach ($uplineSecondaryTypes as $type) {
            $collection = $uplineCollections[$type] ?? null;
            if (!$collection) {
                continue;
            }

            if ($collection->balance < $expectedPerCollection) {
                $difference = $expectedPerCollection - $collection->balance;
                if ($collection->balance + $difference <= 50000000) {
                    $collection->balance += $difference;
                    $collection->save();

                    $collection->transactions()->create([
                        'type' => 'credit',
                        'amount' => $difference,
                        'description' => "Upline sync: {$childrenCount} referrals from {$user->name}",
                    ]);
                }
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
        Collection::createForUser($u->id, $u->getCollectionAccountType());

        $codes = $this->getCollectionCodesForUser($u);
        return Collection::where('user_id', $u->id)
            ->whereIn('type', $codes)
            ->get()
            ->keyBy('type')
            ->all();
    }

    private function getActiveSandboxUserIds(): array
    {
        return DB::table('accounts')
            ->whereIn('type', self::LEGACY_SANDBOX_ACCOUNT_TYPES)
            ->where('active', 1)
            ->pluck('user_id')
            ->unique()
            ->values()
            ->toArray();
    }

    private function isSandboxAccountType(?string $type): bool
    {
        return in_array((string) $type, self::LEGACY_SANDBOX_ACCOUNT_TYPES, true);
    }

    private function normalizeSandboxSubtype(?string $subtype): string
    {
        if (!$subtype) {
            return Account::SUBTYPE_USAHAWAN;
        }

        $normalized = strtolower(trim($subtype));
        if (isset(self::COLLECTION_CODES_BY_SUBTYPE[$normalized])) {
            return $normalized;
        }

        return Account::SUBTYPE_USAHAWAN;
    }

    private function getCollectionCodesForUser(User $user): array
    {
        $subtype = $this->normalizeSandboxSubtype($user->getSandboxSubtype());
        $accountType = $user->getCollectionAccountType();

        $codes = CollectionType::forAccountType($accountType)->pluck('code')->values()->all();
        if (!empty($codes)) {
            return $codes;
        }

        return self::COLLECTION_CODES_BY_SUBTYPE[$subtype] ?? self::COLLECTION_CODES_BY_SUBTYPE[Account::SUBTYPE_USAHAWAN];
    }

    private function getCollectionDisplayName(string $code): string
    {
        return self::COLLECTION_LABELS[$code] ?? ucwords(str_replace('_', ' ', $code));
    }
}
