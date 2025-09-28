<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ModifyAccountSubs extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountTypes = AccountType::all()->keyBy('name');

        // Update accounts
        DB::table('accounts')->get()->each(function($account) use ($accountTypes) {
            $typeName = $account->type;
            $accountTypeId = $accountTypes[$typeName]->id ?? null;
            if ($accountTypeId) {
                DB::table('accounts')->where('id', $account->id)->update([
                    'account_type_id' => $accountTypeId
                ]);
            }
        });

        // Update subscriptions
        DB::table('subscriptions')->get()->each(function($subscription) use ($accountTypes) {
            $planName = $subscription->plan;
            $accountTypeId = $accountTypes[$planName]->id ?? null;
            if ($accountTypeId) {
                DB::table('subscriptions')->where('id', $subscription->id)->update([
                    'account_type_id' => $accountTypeId
                ]);
            }
        });
    }
}
