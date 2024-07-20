<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Work;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkPolicy
{
    use HandlesAuthorization;

    public function before(): bool | null
    {
        /** @var \App\Models\User | null */
        $userAuth = Auth::user();

        if ($userAuth === null) return false;

        if (
            $userAuth->hasRole(RoleEnum::ADMIN->value)
            || $userAuth->hasRole(RoleEnum::DEVELOPER->value)
        )
            return true;

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        return false;
    }

    public function daily(User $user): bool
    {
        if ($user->canAny([
            PermissionEnum::WORK_READ->value,
            PermissionEnum::WORK_READTRASHED->value
        ]))
            return true;

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Work $work): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if ($user->canAny([
            PermissionEnum::WORK_READ->value,
            PermissionEnum::WORK_READTRASHED->value,
        ]))
            return true;

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->can(PermissionEnum::KPI_CREATE->value))
            return true;

        if ($user->can(PermissionEnum::WORK_CREATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Work $work): bool
    {
        if ($user->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        if (
            $user->can(PermissionEnum::WORK_UPDATE->value)
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Work $work): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Work $work): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Work $work): bool
    {
        return false;
    }
}
