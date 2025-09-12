<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManualSBUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Shamsiah Ahmad',
                'email' => 'shamsiahahmad75@gmail.com',
                'serial' => 'SB24041948',
            ],
            [
                'name' => 'Jasmani Masot',
                'email' => 'jasupi63@gmail.com',
                'serial' => 'SB24101954',
            ],
        ];

        foreach ($users as $u) {
            // Check if user already exists
            $user = DB::table('users')->where('email', $u['email'])->first();
            if ($user) {
                $userId = $user->id;
            } else {
                $userId = DB::table('users')->insertGetId([
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make('password123'), // default password
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Ensure profile exists
            $profile = DB::table('profiles')->where('user_id', $userId)->first();
            if (!$profile) {
                DB::table('profiles')->insert([
                    'user_id' => $userId,
                    'full_name' => $u['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Ensure sandbox account exists
            $account = DB::table('accounts')
                ->where('user_id', $userId)
                ->where('type', 'sandbox')
                ->first();

            if (!$account) {
                DB::table('accounts')->insert([
                    'user_id' => $userId,
                    'type' => 'sandbox',
                    'active' => true,
                    'expires_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'serial_number' => $u['serial'],
                ]);
            }
        }
    }
}
