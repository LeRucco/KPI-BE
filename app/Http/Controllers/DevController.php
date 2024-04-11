<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;

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

    public function hesoyam()
    {
        $users = User::where('full_name', '!=', 'developer')->get(['id', 'nrp', 'full_name']);
        return collect($users->map(function ($user) {
            $token = $user->createToken('Token ' . $user->nrp, [], Carbon::now()->addDays(14));
            return [
                'name' => $user->full_name,
                'token' => $token->plainTextToken
            ];
        }));
    }

    public function watercolor()
    {
        $users = User::all();
        return collect($users->map(function ($user) {
            return [
                'User' => $user->full_name,
                'All' => $user->getAllPermissions()->pluck('name'),
                'Via Roles' => $user->getPermissionsViaRoles()->pluck('name'),
                'Direct' => $user->getDirectPermissions()->pluck('name')
            ];
        }));
    }
}
