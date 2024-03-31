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
            return ['name' => $role->value, 'guard_name' => 'web']; // TODO guard_name api ?
        });

        $permissionCollection = collect(PermissionEnum::cases())->map(function (PermissionEnum $permission) {
            return ['name' => $permission->value,  'guard_name' => 'web']; // TODO guard_name api ?
        });

        // Role::create(['name' => 'writer']);
        // Permission::create(['name' => 'edit articles']);

        Role::insert($rolesCollection->toArray());
        Permission::insert($permissionCollection->toArray());

        /** Defining User Admin in AppServiceProvider */
        // $roleSuperAdmin = Role::findByName(RoleEnum::SUPER_ADMIN->value);
        // $roleSuperAdmin->givePermissionTo($permissionCollection->pluck('name'));

        $roleAdmin = Role::findByName(RoleEnum::ADMIN->value);
        $roleAdmin->givePermissionTo($permissionCollection->pluck('name'));

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

            PermissionEnum::JOB_READ,

            PermissionEnum::JOBRATIO_READ,

            PermissionEnum::PAYCHECKFILE_READ,
        ]);
    }
}
