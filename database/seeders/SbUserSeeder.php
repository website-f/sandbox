<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Referral;
use App\Models\Collection;
use App\Models\Account;

class SbUserSeeder extends Seeder
{
    public function run(): void
    {
        // Get users with SB in their name
        $users = User::where('name', 'like', 'SB%')->pluck('id')->toArray();

        // Get users with referral code SB%
        $referrals = Referral::where('ref_code', 'like', 'SB%')->pluck('user_id')->toArray();

        // Merge lists
        $userIds = array_unique(array_merge($users, $referrals));
        $users   = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            // 1. Ensure collection
            $collection = Collection::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );

            // 2. Credit RM300 once only
            $alreadyCredited = $collection->transactions()
                ->where('description', 'SB Initial Bonus')
                ->exists();

            if (!$alreadyCredited) {
                $collection->credit(30000, 'SB Initial Bonus');
            }

            // 3. Ensure sandbox account
            $sandbox = Account::firstOrCreate(
                ['user_id' => $user->id, 'type' => 'sandbox'],
                ['active' => 1]
            );

            if (empty($sandbox->serial_number)) {
                if (str_starts_with($user->name, 'SB')) {
                    // Reuse user->name as serial
                    $sandbox->serial_number = $user->name;
                } else {
                    // Generate new serial
                    $sandbox->serial_number = Account::generateSerial('sandbox');
                }
                $sandbox->save();
            }
        }
    }
}
