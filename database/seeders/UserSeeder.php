<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin Academy',
            'email' => 'admin@academy.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        $admin->assignRole('admin');

        $gv = User::create([
            'name' => 'Giang Vien A',
            'email' => 'gv@academy.com',
            'password' => Hash::make('password123'),
            'role' => 'giang_vien',
        ]);
        $gv->assignRole('giang_vien');

        $hv = User::create([
            'name' => 'Hoc Vien B',
            'email' => 'hv@academy.com',
            'password' => Hash::make('password123'),
            'role' => 'hoc_vien',
        ]);
        $hv->assignRole('hoc_vien');
    }
}
