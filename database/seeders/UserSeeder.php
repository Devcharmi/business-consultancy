<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Create "Super Admin" role if not exists
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // ✅ Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'netlink@gmail.com'],
            [
                'username' => 'netlink',
                'name' => 'netlink',
                'password' => Hash::make('netlink@123'),
                'original_password' => 'netlink@123',
            ]
        );

        // ✅ Assign role
        if (! $superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
