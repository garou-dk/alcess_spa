<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Admin'],
            ['role_name' => 'Staff'],
            ['role_name' => 'Customer'],
        ];

        foreach ($roles as $key => $value) {
            Role::firstOrCreate(['role_name' => $value['role_name']], $value);
        }
    }
}
