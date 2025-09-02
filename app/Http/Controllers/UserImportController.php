<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Referral;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Hash;

class UserImportController extends Controller
{
    public function showForm()
    {
        return view('users.import');
    }

    public function import(Request $request)
    {
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

            // Make display_name unique if empty or duplicate
            $display_name = $row[9] ?? $this->generateUniqueReferralCode($row[9]); // optional column in Excel

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                ]
            );

            // Insert referral record if not exists
            Referral::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ref_code' => $display_name,
                    'parent_id' => null, // optional: set parent_id if you have referral column in Excel
                    'root_id' => null,
                    'level' => 1,
                    'direct_children' => 0,
                ]
            );
        }

        return back()->with('success', 'Users imported successfully!');
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
