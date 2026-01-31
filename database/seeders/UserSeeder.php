<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'full_name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin@123'),
                'role_id' => Role::query()->where('role_name', 'Admin')->valueOrFail('role_id'),
                'image' => null,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($accounts as $key => $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account
            );
        }
    }
}
