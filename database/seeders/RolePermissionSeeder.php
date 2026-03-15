<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Tạo Permissions
        $permissions = [
            // Nhóm user_management (chỉ admin)
            'manage_users',
            'create_user',
            'edit_user',
            'delete_user',
            'view_users',

            // Nhóm schedule (admin + giang_vien)
            'manage_schedule',
            'view_schedule',
            'request_change_schedule',

            // Nhóm class_management (admin + giang_vien)
            'manage_classes',
            'view_classes',
            'manage_attendance',
            'view_attendance',

            // Nhóm grade_management
            'manage_grades',
            'view_grades',

            // Nhóm evaluation
            'manage_evaluation',
            'evaluate_student',
            'evaluate_course',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Tạo ROLES và gán permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $giangVien = Role::create(['name' => 'giang_vien']);
        $giangVien->givePermissionTo([
            'view_schedule',
            'request_change_schedule',
            'view_classes',
            'manage_attendance',
            'view_attendance',
            'manage_grades',
            'view_grades',
            'evaluate_student',
        ]);

        $hocVien = Role::create(['name' => 'hoc_vien']);
        $hocVien->givePermissionTo([
            'view_schedule',
            'view_attendance',
            'view_grades',
            'evaluate_course',
        ]);
    }
}
