<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'rizqmall', 'base_price' => 20.00],
            ['name' => 'sandbox', 'base_price' => 300.00],
            ['name' => 'sandbox remaja', 'base_price' => 300.00],
        ];

        foreach ($types as $type) {
            AccountType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
