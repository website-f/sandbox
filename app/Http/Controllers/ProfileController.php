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
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\ReferralTreeService;
use App\Models\{Profile,Business,Education,Course,NextOfKin,Affiliation};

class ProfileController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        return view('profile.index', [
            'profile'      => Profile::firstOrCreate(['user_id' => $userId]),
            'business'     => Business::firstOrCreate(['user_id' => $userId]),
            'education'    => Education::firstOrCreate(['user_id' => $userId]),
            'bank'    => BankDetail::firstOrCreate(['user_id' => $userId]),
            'courses'      => Course::where('user_id', $userId)->get(),
            'pewaris' => Pewaris::where('user_id', $userId)->get(),
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


public function redirectToRizqmall(Request $request)
{
    $query = http_build_query([
        'user_id' => $request->user()->id,
        'email'   => $request->user()->email,

    ]);

    $baseUrl = env('RIZQMALL_BASE_URL', 'http://rizqmall.test'); // fallback just in case
    return redirect("{$baseUrl}/select-store-category?{$query}");
}


}

