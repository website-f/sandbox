<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserImportController extends Controller
{
    public function showForm()
    {
        return view('users.import');
    }

    public function import(Request $request)
{
    set_time_limit(300);
    $request->validate([
        'file' => 'required|mimes:xlsx,csv,txt',
    ]);

    $file = $request->file('file');
    $data = Excel::toArray([], $file);

    foreach ($data[0] as $row) {
        if (!isset($row[0]) || !isset($row[1])) continue;

        $name = $row[1];
        $email = $row[4];
        $password = 'password123';

        $display_name = $row[9] ?? $this->generateUniqueReferralCode($row[9]);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
            ]
        );

        $referral = Referral::updateOrCreate(
            ['user_id' => $user->id],
            [
                'ref_code' => $display_name,
                'parent_id' => null,
                'root_id' => null,
                'level' => 1,
                'direct_children' => 0,
            ]
        );

        foreach (['rizqmall','sandbox'] as $type) {
            $isSandboxActive = false;

            if (preg_match('/^SB/i', $referral->ref_code)) {
                $isSandboxActive = ($type === 'sandbox');
            }

            Account::updateOrCreate(
                ['user_id' => $user->id, 'type' => $type],
                ['active' => $isSandboxActive]
            );
        }
    }

    // ✅ After finishing import, run fixer to double-check
    $this->fixSandboxAccounts();

    return back()->with('success', 'Users imported successfully and sandbox accounts fixed!');
}

private function fixSandboxAccounts()
{
    $referrals = Referral::where('ref_code', 'like', 'SB%')->get();

    foreach ($referrals as $referral) {
        $userId = $referral->user_id;

        Account::updateOrCreate(
            ['user_id' => $userId, 'type' => 'sandbox'],
            ['active' => true]
        );

        Account::updateOrCreate(
            ['user_id' => $userId, 'type' => 'rizqmall'],
            ['active' => false]
        );
    }
}


    private function generateUniqueReferralCode($base)
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9]/', '', $base));
        if (empty($code)) $code = 'USER';

        $original = $code;
        $i = 1;

        while (Referral::where('ref_code', $code)->exists()) {
            $code = $original . $i;
            $i++;
        }

        return $code;
    }
}
