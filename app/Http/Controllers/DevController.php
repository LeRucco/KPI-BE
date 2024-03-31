<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;

class DevController extends Controller
{

    const route = 'dev';

    public function roleEnum()
    {
        // return RoleEnum::cases();
        return collect(RoleEnum::cases())->map(function (RoleEnum $role) {
            return ['name' => $role->value];
        });
    }

    public function permissionEnum()
    {
        // return PermissionEnum::cases();
        return collect(PermissionEnum::cases())->map(function (PermissionEnum $permission) {
            return ['name' => $permission->value];
        })->toArray(); //->pluck('name');
    }
}
