<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Profile;
use App\Models\Referral;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use App\Services\ReferralTreeService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsahawanSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test.usahawan@sandbox.test'],
            [
                'name' => 'Test Usahawan',
                'password' => Hash::make('password123'),
                'sandbox_type' => 'usahawan',
                'rizqmall_activated_at' => now(),
                'rizqmall_stores_quota' => 1,
            ]
        );

        // Profile
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'Test Usahawan',
                'dob' => Carbon::now()->subYears(30)->format('Y-m-d'),
                'phone' => '0123456001',
                'country' => 'Malaysia',
                'state' => 'Selangor',
                'city' => 'Shah Alam',
            ]
        );

        // Role
        $role = Role::where('name', 'Entrepreneur')->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // Account types
        $sandboxAccountType = AccountType::where('name', 'sandbox_usahawan')->first();
        $rizqmallAccountType = AccountType::where('name', 'rizqmall')->first();

        // Sandbox Account (active)
        Account::updateOrCreate(
            ['user_id' => $user->id, 'type' => Account::TYPE_SANDBOX, 'subtype' => Account::SUBTYPE_USAHAWAN],
            [
                'active' => true,
                'serial_number' => Account::generateUniqueSerial(Account::TYPE_SANDBOX, Account::SUBTYPE_USAHAWAN),
                'account_type_id' => $sandboxAccountType?->id,
            ]
        );

        // RizqMall Account (active, for vendor)
        Account::updateOrCreate(
            ['user_id' => $user->id, 'type' => Account::TYPE_RIZQMALL],
            [
                'active' => true,
                'serial_number' => Account::generateUniqueSerial(Account::TYPE_RIZQMALL),
                'account_type_id' => $rizqmallAccountType?->id,
                'expires_at' => Carbon::now()->addYear(),
            ]
        );

        // Collections
        Collection::createForUser($user->id, CollectionType::ACCOUNT_SANDBOX_USAHAWAN);

        // Referral record
        if (!Referral::where('user_id', $user->id)->exists()) {
            $treeService = new ReferralTreeService();
            Referral::create([
                'user_id' => $user->id,
                'parent_id' => null,
                'root_id' => $user->id,
                'level' => 1,
                'direct_children' => 0,
                'ref_code' => $treeService->generateRefCode($user),
            ]);
        }

        // Subscription (active)
        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'plan' => 'sandbox'],
            [
                'amount' => 30000,
                'status' => 'paid',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'account_type_id' => $sandboxAccountType?->id,
            ]
        );

        $this->command->info("Test Usahawan user created: test.usahawan@sandbox.test / password123");
    }
}
