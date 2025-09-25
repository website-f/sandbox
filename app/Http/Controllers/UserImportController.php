<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        set_time_limit(3000);
    
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt',
        ]);
    
        $file = $request->file('file');
        $data = Excel::toArray([], $file);
    
        foreach ($data[0] as $index => $row) {
            if ($index === 0) continue; // skip header
    
            // 🧹 Clean values
           $clean = function($val) {
                if (is_null($val)) return null;
                $val = trim((string)$val);
            
                if ($val === '' || $val === '\\N' || strtoupper($val) === 'NULL' || $val === '#####') {
                    return null;
                }
            
                return $val;
            };

    
            $name          = $clean($row[2] ?? null);
            $email         = $clean($row[3] ?? null);
            $sandboxNumber = $clean($row[21] ?? null);
            $pptemNumber   = $clean($row[22] ?? null);
            $pptemSponsor  = $clean($row[25] ?? null);
            $expireDateRaw = $clean($row[31] ?? null);
    
            if (! $name || ! $email) continue;
    
            // 📌 Fix sandboxNumber rule
            if ($sandboxNumber && !str_starts_with($sandboxNumber, 'SB')) {
                $sandboxNumber = null;
            }
    
            // 📌 Fix pptem rules
            if ($pptemNumber && !str_starts_with($pptemNumber, 'PPTEM')) {
                $pptemNumber = null;
            }
            if ($pptemSponsor && !str_starts_with($pptemSponsor, 'PPTEM')) {
                $pptemSponsor = null;
            }
    
            // 📌 Fix expire date (skip "#####")
            $expireDate = null;
            if ($expireDateRaw && $expireDateRaw !== '#####') {
                try {
                    if (is_numeric($expireDateRaw)) {
                        // Excel serial number
                        $expireDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($expireDateRaw);
                    } else {
                        // Try parsing string date (d/m/Y H:i or Y-m-d etc.)
                        $expireDate = Carbon::parse($expireDateRaw);
                    }
                } catch (\Exception $e) {
                    $expireDate = null;
                }
            }
    
            // 👤 Create or update user
            $user = User::where('email', $email)->first();
    
            if ($user) {
                // update only name
                $user->update(['name' => $name]);
            } else {
                // new user with default password
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                ]);
            }
    
            // 📌 Accounts
            foreach (['rizqmall', 'sandbox'] as $type) {
               $isActive = ($type === 'sandbox' && $sandboxNumber);
           
               $data = [
                   'active' => $isActive,
                   'serial_number' => null, // default null
               ];
           
               if ($type === 'sandbox' && $sandboxNumber) {
                   // prevent duplicate sandbox serials
                   $exists = Account::where('serial_number', $sandboxNumber)
                       ->where('user_id', '<>', $user->id)
                       ->exists();
           
                   if (! $exists) {
                       $data['serial_number'] = $sandboxNumber;
                   }
               }
           
               Account::updateOrCreate(
                   ['user_id' => $user->id, 'type' => $type],
                   $data
               );
            }           

    
            // 📌 PPTEM row
            if ($pptemNumber) {
                \DB::table('pptem')->updateOrInsert(
                    ['user_id' => $user->id],
                    [
                        'pptem_number' => $pptemNumber,
                        'pptem_ref'    => $pptemSponsor,
                        'expire_date'  => $expireDate,
                        'updated_at'   => now(),
                        'created_at'   => now(),
                    ]
                );
            }
        }
    
        // ✅ Fix wrongly activated sandbox accounts
        $this->fixSandboxAccounts();
    
        return back()->with('success', 'Users imported and updated successfully!');
    }
    
    private function fixSandboxAccounts()
    {
        $accounts = Account::where('type', 'sandbox')->get();
    
        foreach ($accounts as $account) {
            if (! $account->serial_number || ! str_starts_with($account->serial_number, 'SB')) {
                $account->update([
                    'serial_number' => null,
                    'active' => false,
                ]);
            }
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
