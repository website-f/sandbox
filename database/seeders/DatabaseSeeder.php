<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AccountTypeSeeder;
use Database\Seeders\CollectionTypeSeeder;
use Database\Seeders\ModifyAccountSubs;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountTypeSeeder::class,
            CollectionTypeSeeder::class, // populate collection types for all sandbox types
            ModifyAccountSubs::class, // populate account_type_id in accounts & subscriptions
        ]);
    }
}
