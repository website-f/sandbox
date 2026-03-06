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

class TestAwamSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test.awam@sandbox.test'],
            [
                'name' => 'Test Awam',
                'password' => Hash::make('password123'),
                'sandbox_type' => 'awam',
            ]
        );

        // Profile (adult)
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'Test Awam',
                'dob' => Carbon::now()->subYears(35)->format('Y-m-d'),
                'phone' => '0123456003',
                'country' => 'Malaysia',
                'state' => 'Penang',
                'city' => 'George Town',
            ]
        );

        // Role
        $role = Role::where('name', 'Entrepreneur')->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // Account type
        $sandboxAccountType = AccountType::where('name', 'sandbox_awam')->first();

        // Sandbox Account (active)
        Account::updateOrCreate(
            ['user_id' => $user->id, 'type' => Account::TYPE_SANDBOX, 'subtype' => Account::SUBTYPE_AWAM],
            [
                'active' => true,
                'serial_number' => Account::generateUniqueSerial(Account::TYPE_SANDBOX, Account::SUBTYPE_AWAM),
                'account_type_id' => $sandboxAccountType?->id,
            ]
        );

        // Collections
        Collection::createForUser($user->id, CollectionType::ACCOUNT_SANDBOX_AWAM);

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

        $this->command->info("Test Awam user created: test.awam@sandbox.test / password123");
    }
}
