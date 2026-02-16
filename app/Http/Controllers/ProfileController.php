<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use App\Models\Pewaris;
use App\Models\Referral;
use App\Models\Collection;
use App\Models\BankDetail;
use App\Models\AccountType;
use App\Models\CollectionType;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\ReferralTreeService;
use App\Models\{Profile,Business,Education,Course,NextOfKin,Affiliation};

class ProfileController extends Controller
{
    /**
     * Display the user's profile (read-only view)
     */
    public function index()
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Get user's accounts
        $accounts = $user->accounts()->get();

        // Get user's subscription history
        $subscriptions = Subscription::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile.index', [
            'profile'       => Profile::firstOrCreate(['user_id' => $userId]),
            'business'      => Business::firstOrCreate(['user_id' => $userId]),
            'education'     => Education::firstOrCreate(['user_id' => $userId]),
            'bank'          => BankDetail::firstOrCreate(['user_id' => $userId]),
            'courses'       => Course::where('user_id', $userId)->get(),
            'pewaris'       => Pewaris::with('linkedUser.accounts')->where('user_id', $userId)->get(),
            'affiliation'   => Affiliation::firstOrCreate(['user_id' => $userId]),
            'accounts'      => $accounts,
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Display the profile edit form
     */
    public function edit()
    {
        $userId = Auth::id();

        return view('profile.edit', [
            'profile'      => Profile::firstOrCreate(['user_id' => $userId]),
            'business'     => Business::firstOrCreate(['user_id' => $userId]),
            'education'    => Education::firstOrCreate(['user_id' => $userId]),
            'bank'         => BankDetail::firstOrCreate(['user_id' => $userId]),
            'courses'      => Course::where('user_id', $userId)->get(),
            'pewaris'      => Pewaris::with('linkedUser.accounts')->where('user_id', $userId)->get(),
            'affiliation'  => Affiliation::firstOrCreate(['user_id' => $userId]),
        ]);
    }

public function updateProfile(Request $request)
{
    $request->validate([
        'photo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $profile = Profile::where('user_id', Auth::id())->firstOrFail();
    
    $data = $request->except('photo_path');
    
    // Handle photo upload
    if ($request->hasFile('photo_path')) {
        // Delete old photo if exists
        if ($profile->photo_path && \Storage::disk('public')->exists($profile->photo_path)) {
            Storage::disk('public')->delete($profile->photo_path);
        }
        
        // Store new photo
        $path = $request->file('photo_path')->store('profile_photos', 'public');
        $data['photo_path'] = $path;
    }
    
    // Handle photo removal
    if ($request->has('remove_photo') && $request->remove_photo == '1') {
        if ($profile->photo_path && \Storage::disk('public')->exists($profile->photo_path)) {
            \Storage::disk('public')->delete($profile->photo_path);
        }
        $data['photo_path'] = null;
    }
    
    $profile->update($data);
    
    return back()->with('success', 'Profile updated successfully');
}

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);
        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password incorrect']);
        }
        $user->update(['password' => bcrypt($request->new_password)]);
        return back()->with('success', 'Password updated');
    }

    public function updateBusiness(Request $r)
    {
        Business::updateOrCreate(
            ['user_id' => Auth::id()],
            $r->all()
        );
        return back()->with('success','Business updated');
    }

    public function updateEducation(Request $r)
    {
        Education::updateOrCreate(
            ['user_id' => Auth::id()],
            $r->all()
        );
        return back()->with('success','Education updated');
    }

    public function updateBank(Request $r)
    {
        BankDetail::updateOrCreate(
            ['user_id' => Auth::id()],
            $r->all()
        );
        return back()->with('success','Bank Details updated');
    }

    public function updateCourse(Request $r)
    {
        Course::create($r->all()+['user_id'=>Auth::id()]);
        return back()->with('success','Course added');
    }

    public function updateNextOfKin(Request $r)
    {
        NextOfKin::updateOrCreate(
            ['user_id' => Auth::id()],
            $r->all()
        );
        return back()->with('success','Next of Kin updated');
    }

    public function updateAffiliation(Request $r)
    {
        Affiliation::updateOrCreate(
            ['user_id' => Auth::id()],
            $r->all()
        );
        return back()->with('success','Affiliation updated');
    }

    public function storePewaris(Request $request, ReferralTreeService $tree)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:pewaris,email',
            'address' => 'nullable|string',
            'dob' => 'nullable|date',
        ]);

        // Create Pewaris
        $pewaris = Pewaris::create([
            'user_id' => $user->id,
            'name' => $data['name'] ?? null,
            'relationship' => $data['relationship'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'dob' => $data['dob'] ?? null,
        ]);

        // Only create linked user if email provided
        if (!empty($data['email'])) {
            $linkedUser = User::where('email', $data['email'])->first();
            if (!$linkedUser) {
                $linkedUser = User::create([
                    'name' => $data['name'] ?? 'No Name',
                    'email' => $data['email'],
                    'password' => Hash::make(Str::random(12)),
                ]);
            }

            $role = Role::where('name', 'Entrepreneur')->first();
            if ($role) {
                $linkedUser->roles()->syncWithoutDetaching([$role->id]);
            }

            $defaultSubtype = (!empty($data['dob']) && Account::isEligibleForRemaja($data['dob']))
                ? Account::SUBTYPE_REMAJA
                : Account::SUBTYPE_AWAM;

            $sandboxAccountType = AccountType::where('name', 'sandbox_' . $defaultSubtype)->first()
                ?? AccountType::where('name', 'sandbox')->first();
            $rizqmallAccountType = AccountType::where('name', 'rizqmall')->first();

            Account::firstOrCreate(
                ['user_id' => $linkedUser->id, 'type' => Account::TYPE_RIZQMALL],
                [
                    'account_type_id' => $rizqmallAccountType?->id,
                    'active' => false,
                ]
            );

            $legacyTypeToSubtype = [
                'sandbox remaja' => Account::SUBTYPE_REMAJA,
                'sandbox awam' => Account::SUBTYPE_AWAM,
                'sandbox usahawan' => Account::SUBTYPE_USAHAWAN,
            ];
            $existingSandboxAccount = $linkedUser->accounts()
                ->whereIn('type', array_merge([Account::TYPE_SANDBOX], array_keys($legacyTypeToSubtype)))
                ->first();

            if (!$existingSandboxAccount) {
                Account::create([
                    'user_id' => $linkedUser->id,
                    'type' => Account::TYPE_SANDBOX,
                    'subtype' => $defaultSubtype,
                    'account_type_id' => $sandboxAccountType?->id,
                    'active' => false,
                ]);
            } else {
                $originalType = $existingSandboxAccount->type;
                $resolvedSubtype = $existingSandboxAccount->subtype
                    ?: ($legacyTypeToSubtype[$originalType] ?? $defaultSubtype);
                $resolvedSandboxAccountType = AccountType::where('name', 'sandbox_' . $resolvedSubtype)->first()
                    ?? AccountType::where('name', 'sandbox')->first();

                $existingSandboxAccount->type = Account::TYPE_SANDBOX;
                $existingSandboxAccount->subtype = $resolvedSubtype;
                $existingSandboxAccount->account_type_id = $resolvedSandboxAccountType?->id ?? $existingSandboxAccount->account_type_id;
                $existingSandboxAccount->active = (bool) $existingSandboxAccount->active;
                $existingSandboxAccount->save();
            }

            $linkedUser->update(['sandbox_type' => $defaultSubtype]);

            // Create collections aligned to default subtype
            $collectionAccountType = match ($defaultSubtype) {
                Account::SUBTYPE_REMAJA => CollectionType::ACCOUNT_SANDBOX_REMAJA,
                Account::SUBTYPE_AWAM => CollectionType::ACCOUNT_SANDBOX_AWAM,
                default => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
            };
            Collection::createForUser($linkedUser->id, $collectionAccountType);

            // Link back
            $pewaris->linked_user_id = $linkedUser->id;
            $pewaris->save();

            // Ensure linked user is placed under current user in referral tree
            $this->ensurePewarisReferralPlacement($user, $linkedUser, $tree);
        }

        return back()->with('success', 'Pewaris added successfully!');
    }

    public function assignPewarisSandbox(Request $request, Pewaris $pewaris, ReferralTreeService $tree)
    {
        $owner = auth()->user();

        if ($pewaris->user_id !== $owner->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'sandbox_subtype' => 'required|in:usahawan,remaja,awam',
        ]);

        $targetSubtype = $validated['sandbox_subtype'];

        if ($targetSubtype === Account::SUBTYPE_REMAJA && !$pewaris->isEligibleForRemaja()) {
            return back()->with('error', 'Sandbox Remaja is only for ages 11-20.');
        }

        $linkedUser = $pewaris->linkedUser;

        // If linked user doesn't exist yet, create one from pewaris details
        if (!$linkedUser) {
            if (empty($pewaris->email)) {
                return back()->with('error', 'Please provide an email for this next of kin first.');
            }

            $linkedUser = User::where('email', $pewaris->email)->first();
            if (!$linkedUser) {
                $linkedUser = User::create([
                    'name' => $pewaris->name ?: 'No Name',
                    'email' => $pewaris->email,
                    'password' => Hash::make(Str::random(12)),
                    'sandbox_type' => $targetSubtype,
                ]);
            }

            $role = Role::where('name', 'Entrepreneur')->first();
            if ($role) {
                $linkedUser->roles()->syncWithoutDetaching([$role->id]);
            }

            $pewaris->linked_user_id = $linkedUser->id;
            $pewaris->save();
        }

        // Ensure linked user is under owner in referral tree
        $this->ensurePewarisReferralPlacement($owner, $linkedUser, $tree);

        // Ensure RizqMall account exists
        $rizqmallAccountType = AccountType::where('name', 'rizqmall')->first();
        Account::firstOrCreate(
            ['user_id' => $linkedUser->id, 'type' => Account::TYPE_RIZQMALL],
            [
                'account_type_id' => $rizqmallAccountType?->id,
                'active' => false,
            ]
        );

        // Normalize legacy sandbox account rows and ensure one sandbox account exists
        $legacyTypeToSubtype = [
            'sandbox remaja' => Account::SUBTYPE_REMAJA,
            'sandbox awam' => Account::SUBTYPE_AWAM,
            'sandbox usahawan' => Account::SUBTYPE_USAHAWAN,
        ];

        $sandboxAccount = $linkedUser->accounts()->where('type', Account::TYPE_SANDBOX)->first();
        $legacySandboxAccounts = $linkedUser->accounts()
            ->whereIn('type', array_keys($legacyTypeToSubtype))
            ->get();

        if (!$sandboxAccount && $legacySandboxAccounts->isNotEmpty()) {
            $sandboxAccount = $legacySandboxAccounts->first();
            $sandboxAccount->type = Account::TYPE_SANDBOX;
            if (!$sandboxAccount->subtype) {
                $sandboxAccount->subtype = $legacyTypeToSubtype[$sandboxAccount->getOriginal('type')] ?? null;
            }
        }

        if ($sandboxAccount && $legacySandboxAccounts->isNotEmpty()) {
            foreach ($legacySandboxAccounts as $legacySandboxAccount) {
                if ($sandboxAccount->id === $legacySandboxAccount->id) {
                    continue;
                }

                if (!$sandboxAccount->serial_number && $legacySandboxAccount->serial_number) {
                    $sandboxAccount->serial_number = $legacySandboxAccount->serial_number;
                }

                if (
                    !$sandboxAccount->subtype
                    && isset($legacyTypeToSubtype[$legacySandboxAccount->type])
                ) {
                    $sandboxAccount->subtype = $legacyTypeToSubtype[$legacySandboxAccount->type];
                }

                $legacySandboxAccount->delete();
            }
        }

        $sandboxAccountType = AccountType::where('name', 'sandbox_' . $targetSubtype)->first()
            ?? AccountType::where('name', 'sandbox')->first();

        if (!$sandboxAccount) {
            $sandboxAccount = new Account([
                'user_id' => $linkedUser->id,
                'type' => Account::TYPE_SANDBOX,
            ]);
        }

        $sandboxAccount->subtype = $targetSubtype;
        $sandboxAccount->account_type_id = $sandboxAccountType?->id;
        $sandboxAccount->active = true;
        if (!$sandboxAccount->serial_number) {
            $sandboxAccount->serial_number = Account::generateUniqueSerial(Account::TYPE_SANDBOX, $targetSubtype);
        }
        $sandboxAccount->save();

        // Sync linked user subtype for fallback logic
        $linkedUser->sandbox_type = $targetSubtype;
        $linkedUser->save();

        // Ensure subtype-specific collections exist
        $collectionAccountType = match ($targetSubtype) {
            Account::SUBTYPE_REMAJA => CollectionType::ACCOUNT_SANDBOX_REMAJA,
            Account::SUBTYPE_AWAM => CollectionType::ACCOUNT_SANDBOX_AWAM,
            default => CollectionType::ACCOUNT_SANDBOX_USAHAWAN,
        };
        Collection::createForUser($linkedUser->id, $collectionAccountType);

        $label = ucfirst($targetSubtype);
        return back()->with('success', "{$pewaris->name} has been added to Sandbox {$label} under your referral tree.");
    }

public function destroyPewaris(Pewaris $pewaris)
{
    $user = auth()->user();

    // Only allow deleting own pewaris
    if ($pewaris->user_id !== $user->id) {
        return back()->with('error', 'Unauthorized action.');
    }

    // If there's a linked user, we should handle that too
    if ($pewaris->linked_user_id) {
        // Optional: You may want to delete or deactivate the linked user
        // For now, we'll just unlink them
        $linkedUser = $pewaris->linkedUser;
        if ($linkedUser) {
            // Deactivate the linked user's accounts instead of deleting
            $linkedUser->accounts()->update(['active' => false]);
        }
    }

    $pewaris->delete();

    return back()->with('success', 'Pewaris deleted successfully!');
}

private function ensurePewarisReferralPlacement(User $owner, User $linkedUser, ReferralTreeService $tree): void
{
    $ownerReferral = $owner->referral;
    if (!$ownerReferral) {
        $ownerReferral = Referral::create([
            'user_id' => $owner->id,
            'parent_id' => null,
            'root_id' => $owner->id,
            'level' => 1,
            'direct_children' => 0,
            'ref_code' => $tree->generateRefCode($owner),
        ]);
    }

    $rootId = $ownerReferral->root_id ?: $owner->id;
    $linkedReferral = $linkedUser->referral;

    if (!$linkedReferral) {
        $tree->attach($owner, $linkedUser);
        return;
    }

    $expectedLevel = ($ownerReferral->level ?? 1) + 1;
    $linkedReferral->update([
        'parent_id' => $owner->id,
        'root_id' => $rootId,
        'level' => $expectedLevel,
    ]);
}

public function redirectToRizqmall(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        // Check if user has RizqMall account
        $rizqmallAccount = $user->accounts()
            ->where('type', 'rizqmall')
            ->first();

        if (!$rizqmallAccount) {
            return back()->with('error', 'You do not have a RizqMall account. Please subscribe first.');
        }

        // Check if account is active
        if (!$rizqmallAccount->active) {
            return back()->with('error', 'Your RizqMall account is not active. Please contact support.');
        }

        // Check if expired
        if ($rizqmallAccount->expires_at && $rizqmallAccount->expires_at->isPast()) {
            return back()->with('error', 'Your RizqMall subscription has expired. Please renew.');
        }

        Log::info('SSO redirect to RizqMall', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Build SSO query parameters
        $query = http_build_query([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'token' => $this->generateSsoToken($user), // Optional: for extra security
        ]);

        $baseUrl = config('services.rizqmall.base_url', 'http://rizqmall.test');
        
        return redirect("{$baseUrl}/auth/redirect?{$query}");
    }

    /**
     * Handle customer registration/login redirect to RizqMall
     * (For customers who want to browse/buy without vendor subscription)
     */
    public function customerRedirectToRizqmall(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        Log::info('Customer SSO redirect to RizqMall', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Build SSO query parameters
        $query = http_build_query([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'customer' => 'true', // Flag as customer
        ]);

        $baseUrl = config('services.rizqmall.base_url', 'http://rizqmall.test');
        
        return redirect("{$baseUrl}/auth/redirect?{$query}");
    }

    /**
     * Generate a simple SSO token (optional)
     */
    private function generateSsoToken(User $user)
    {
        // Simple token: hash of user data + secret
        $secret = config('services.rizqmall.sso_secret', config('app.key'));
        return hash('sha256', $user->id . $user->email . time() . $secret);
    }

    /**
     * Handle logout from RizqMall (webhook)
     */
    public function handleLogout(Request $request)
    {
        $userId = $request->input('user_id');
        
        Log::info('RizqMall logout webhook received', [
            'user_id' => $userId,
        ]);

        // You can add additional logout logic here
        // e.g., invalidate sessions, update activity logs, etc.

        return response()->json([
            'success' => true,
            'message' => 'Logout processed',
        ]);
    }

}
