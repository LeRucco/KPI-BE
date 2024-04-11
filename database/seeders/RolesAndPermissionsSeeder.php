<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesCollection =  collect(RoleEnum::cases())->map(function (RoleEnum $role) {
            return ['name' => $role->value, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()];
        });

        $permissionCollection = collect(PermissionEnum::cases())->map(function (PermissionEnum $permission) {
            return ['name' => $permission->value,  'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()];
        });

        Role::insert($rolesCollection->toArray());
        Permission::insert($permissionCollection->toArray());

        $roleAdmin = Role::findByName(RoleEnum::ADMIN->value);
        $roleAdmin->givePermissionTo([
            PermissionEnum::KPI_CREATE,
            PermissionEnum::KPI_READ,
            PermissionEnum::KPI_READTRASHED,
            PermissionEnum::KPI_UPDATE,
            PermissionEnum::KPI_DELETE,
        ]);

        $roleDeveloper = Role::findByName(RoleEnum::DEVELOPER->value);
        $roleDeveloper->givePermissionTo($permissionCollection->pluck('name'));

        $roleEmployee = Role::findByName(RoleEnum::EMPLOYEE->value);
        $roleEmployee->givePermissionTo([
            PermissionEnum::ASSIGNMENT_CREATE,
            PermissionEnum::ASSIGNMENT_READ,
            PermissionEnum::ASSIGNMENT_UPDATE,

            PermissionEnum::ASSIGNMENTFILE_READ,

            PermissionEnum::ATTENDANCE_CREATE,
            PermissionEnum::ATTENDANCE_READ,
            PermissionEnum::ATTENDANCE_UPDATE,

            PermissionEnum::ATTENDANCEFILE_READ,

            PermissionEnum::WORK_READ,

            PermissionEnum::WORK_READ,

            PermissionEnum::PAYCHECKFILE_READ,
        ]);
    }
}
