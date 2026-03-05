<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestAccountSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating test accounts for all 3 sandbox types...');

        $this->call([
            TestUsahawanSeeder::class,
            TestRemajaSeeder::class,
            TestAwamSeeder::class,
        ]);

        $this->command->info('All test accounts created successfully!');
        $this->command->info('');
        $this->command->info('Test Accounts:');
        $this->command->info('  Usahawan: test.usahawan@sandbox.test / password123 (can create vendor stores)');
        $this->command->info('  Remaja:   test.remaja@sandbox.test   / password123 (age 16, no vendor access)');
        $this->command->info('  Awam:     test.awam@sandbox.test     / password123 (no vendor access)');
    }
}
