<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@reklame.local',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
            ],
            [
                'name'     => 'Staff Operasional',
                'email'    => 'staff@reklame.local',
                'password' => Hash::make('password'),
                'role'     => 'staff',
            ],
            [
                'name'     => 'Finance',
                'email'    => 'finance@reklame.local',
                'password' => Hash::make('password'),
                'role'     => 'finance',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insertOrIgnore(array_merge($user, [
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
