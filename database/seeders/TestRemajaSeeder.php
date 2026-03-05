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

class TestRemajaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test.remaja@sandbox.test'],
            [
                'name' => 'Test Remaja',
                'password' => Hash::make('password123'),
                'sandbox_type' => 'remaja',
            ]
        );

        // Profile (age 16, within 11-20 range)
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'Test Remaja',
                'dob' => Carbon::now()->subYears(16)->format('Y-m-d'),
                'phone' => '0123456002',
                'country' => 'Malaysia',
                'state' => 'Johor',
                'city' => 'Johor Bahru',
            ]
        );

        // Role
        $role = Role::where('name', 'Entrepreneur')->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // Account type
        $sandboxAccountType = AccountType::where('name', 'sandbox_remaja')->first();

        // Sandbox Account (active)
        Account::updateOrCreate(
            ['user_id' => $user->id, 'type' => Account::TYPE_SANDBOX, 'subtype' => Account::SUBTYPE_REMAJA],
            [
                'active' => true,
                'serial_number' => Account::generateUniqueSerial(Account::TYPE_SANDBOX, Account::SUBTYPE_REMAJA),
                'account_type_id' => $sandboxAccountType?->id,
            ]
        );

        // Collections
        Collection::createForUser($user->id, CollectionType::ACCOUNT_SANDBOX_REMAJA);

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
            ['user_id' => $user->id, 'plan' => 'sandbox_remaja'],
            [
                'amount' => 30000,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'account_type_id' => $sandboxAccountType?->id,
            ]
        );

        $this->command->info("Test Remaja user created: test.remaja@sandbox.test / password123");
    }
}
