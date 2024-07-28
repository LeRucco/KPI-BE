<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Paycheck;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class PaycheckPolicy
{
    use HandlesAuthorization;

    public function before(): bool | null
    {
        /** @var \App\Models\user | null*/
        $userAuth = Auth::user();

        if ($userAuth === null) return false;

        if (
            $userAuth->hasRole(RoleEnum::ADMIN->value)
            || $userAuth->hasRole(RoleEnum::DEVELOPER->value)
        )
            return true;

        return null;
    }

    public function viewAny(User $user): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        return false;
    }

    public function viewAnyFiles(User $user): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        return false;
    }

    public function view(User $user, Paycheck $paycheck): bool
    {
        if ($user->canAny(
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value,
        ))
            return true;

        if (
            $user->canAny([
                PermissionEnum::PAYCHECK_READ->value,
                PermissionEnum::PAYCHECK_READTRASHED->value
            ])
            && $user->id === $paycheck->user_id
        )
            return true;

        return false;
    }

    public function viewFiles(User $user, Paycheck $paycheck): bool
    {
        if ($user->canAny(
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value,
        ))
            return true;

        if (
            $user->canAny([
                PermissionEnum::PAYCHECKFILE_READ->value,
                PERMISSIONENUM::PAYCHECKFILE_READTRASHED->value
            ])
            && $user->id === $paycheck->user_id
        )
            return true;

        return false;
    }

    public function report(User $user): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value,
        ]))
            return true;

        return false;
    }

    public function yearly(User $user)
    {
        if ($user->canAny(
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value,
        ))
            return true;

        if (
            $user->canAny([
                PermissionEnum::PAYCHECK_READ->value,
                PermissionEnum::PAYCHECK_READTRASHED->value
            ])
            && $user->canAny([
                PermissionEnum::PAYCHECKFILE_READ->value,
                PERMISSIONENUM::PAYCHECKFILE_READTRASHED->value
            ])
        )
            return true;

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->can(PermissionEnum::KPI_CREATE->value))
            return true;

        if ($user->can(PermissionEnum::PAYCHECK_CREATE->value))
            return true;

        return false;
    }

    public function createFiles(User $user): bool
    {
        if ($user->can(PermissionEnum::KPI_CREATE->value))
            return true;

        if ($user->can(PermissionEnum::PAYCHECKFILE_CREATE->value))
            return true;

        return false;
    }

    // public function update(User $user, Paycheck $paycheck): bool
    // {
    //     if ($user->can(PermissionEnum::KPI_UPDATE->value))
    //         return true;

    //     if (
    //         $user->can(PermissionEnum::PAYCHECK_UPDATE->value)
    //         && $user->id === $paycheck->user_id
    //     )
    //         return true;

    //     return false;
    // }

    // pub
}
