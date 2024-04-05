<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('spatie:create {userName} {permission}', function (string $userName, string $permission) {
    /** @var \App\Models\User */
    $user = \App\Models\User::where('full_name', '=', $userName)->first();
    $this->info($user);

    $permissionEnum =  \App\Enums\PermissionEnum::from($permission);
    $this->info($permissionEnum->value);

    $user->givePermissionTo($permissionEnum);
    $this->comment('Give permission [' . $permissionEnum->value . '] to ' . $user->full_name);
})->purpose('Give a direct permission to specific user');

Artisan::command('spatie:revoke {userName} {permission}', function (string $userName, string $permission) {
    /** @var \App\Models\User */
    $user = \App\Models\User::where('full_name', '=', $userName)->first();
    $this->info($user);

    $permissionEnum =  \App\Enums\PermissionEnum::from($permission);
    $this->info($permissionEnum->value);

    $user->revokePermissionTo($permissionEnum);
    $this->comment('Revoke permission [' . $permissionEnum->value . '] to ' . $user->full_name);
})->purpose('Revoke a direct permission to specific user');

Artisan::command('spatie:read {userName}', function (string $userName) {
    /** @var \App\Models\User */
    $user = \App\Models\User::where('full_name', '=', $userName)->first();

    $this->info($user);

    $this->comment('All');
    $this->comment($user->getAllPermissions()->pluck('name'));
    $this->comment('Via Roles');
    $this->comment($user->getPermissionsViaRoles()->pluck('name'));
    $this->comment('Direct : ');
    $this->comment($user->getDirectPermissions()->pluck('name'));
})->purpose('Read all permission to specific user');

Artisan::command('spatie:permissions:read', function () {

    $this->comment('All');
    $all = \App\Enums\PermissionEnum::cases();

    foreach ($all as $permission) {
        $this->info($permission->value);
    }
})->purpose('Get all permissions enum');

Artisan::command('spatie:roles:read', function () {

    $this->comment('All');
    $all = \App\Enums\RoleEnum::cases();

    foreach ($all as $role) {
        $this->info($role->value);
    }
})->purpose('Get all roles enum ');
