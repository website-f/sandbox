<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'courses'      => Course::where('user_id', $userId)->get(),
            'nextOfKin'    => NextOfKin::firstOrCreate(['user_id' => $userId]),
            'affiliation'  => Affiliation::firstOrCreate(['user_id' => $userId]),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $profile = Profile::where('user_id', Auth::id())->firstOrFail();
        $profile->update($request->all());
        return back()->with('success', 'Profile updated');
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
}

