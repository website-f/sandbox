<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Account Types:
     * - rizqmall: RizqMall marketplace subscription (RM20/year)
     * - sandbox: Legacy type, maps to sandbox_usahawan
     * - sandbox_usahawan: Sandbox for entrepreneurs (RM300 lifetime)
     * - sandbox_remaja: Sandbox for youth 11-20 years old (RM300 lifetime)
     * - sandbox_awam: Sandbox for general public (RM300 lifetime)
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'rizqmall',
                'base_price' => 20.00,
            ],
            [
                'name' => 'sandbox',
                'base_price' => 300.00,
            ],
            [
                'name' => 'sandbox_usahawan',
                'base_price' => 300.00,
            ],
            [
                'name' => 'sandbox_remaja',
                'base_price' => 300.00,
            ],
            [
                'name' => 'sandbox_awam',
                'base_price' => 300.00,
            ],
        ];

        foreach ($types as $type) {
            AccountType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
