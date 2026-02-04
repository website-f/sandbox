<?php

namespace App\Http\Controllers;

use Storage;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use App\Models\Pewaris;
use App\Models\BankDetail;
use App\Models\AccountType;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'pewaris'       => Pewaris::where('user_id', $userId)->get(),
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
            'pewaris'      => Pewaris::where('user_id', $userId)->get(),
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
        'email' => 'nullable|email|unique:users,email',
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
        'dob' => $data['dob'] ?? null, // save DOB
    ]);

    // Only create linked user if email provided
    if (!empty($data['email'])) {
        $linkedUser = User::create([
            'name' => $data['name'] ?? 'No Name',
            'email' => $data['email'],
            'password' => Hash::make(Str::random(12)),
        ]);

        $role = Role::where('name', 'Entrepreneur')->first();
        $linkedUser->roles()->attach($role);

        // Determine accounts to create
        $accounts = AccountType::whereIn('name', ['rizqmall', 'sandbox'])->get()->keyBy('name');

        // Add sandbox remaja if under 25
        if (!empty($data['dob']) && Carbon::parse($data['dob'])->age < 25) {
            $accounts['sandbox remaja'] = AccountType::where('name', 'sandbox remaja')->first();
        }

        // Create accounts
        foreach ($accounts as $type => $accountType) {
            Account::create([
                'user_id' => $linkedUser->id,
                'type' => $type,
                'account_type_id' => $accountType->id,
                'active' => false,
            ]);
        }

        // Link back
        $pewaris->linked_user_id = $linkedUser->id;
        $pewaris->save();

        // Attach to referral tree
        $tree->attach($user, $linkedUser);
    }

    return back()->with('success', 'Pewaris added successfully!');
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

