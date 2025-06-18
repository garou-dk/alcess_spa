<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'password' => 'Admin@123',
                'role_id' => Role::query()->where('role_name', 'Admin')->valueOrFail('role_id'),
                'image' => null,
            ]
        ];

        foreach ($accounts as $key => $account) {
            User::create($account);
        }
    }
}
