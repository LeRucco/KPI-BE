<?php

namespace App\Http\Controllers;

use App\Data\RolePermission\RolePermissionResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;

class RolePermissionController extends Controller
{
    const route = 'role';

    public function user(User $user)
    {
        // return $user->getRoleNames();
        // return $user->getAllPermissions();

        (array) $data = RolePermissionResponse::from(
            $user->getRoleNames(),
            $user->getAllPermissions()
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }
}
