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
        \App\Models\GiangVienProfile::create([
            'user_id' => $gv->id,
            'ma_giang_vien' => 'GV001',
            'hoc_vi' => 'Thạc sĩ',
            'chuyen_mon' => 'Công nghệ thông tin',
        ]);

        $hv = User::create([
            'name' => 'Hoc Vien B',
            'email' => 'hv@academy.com',
            'password' => Hash::make('password123'),
            'role' => 'hoc_vien',
        ]);
        $hv->assignRole('hoc_vien');
        \App\Models\HocVienProfile::create([
            'user_id' => $hv->id,
            'ma_hoc_vien' => 'HV001',
            'ngay_nhap_hoc' => now(),
            'trang_thai' => 'dang_hoc',
        ]);
    }
}
