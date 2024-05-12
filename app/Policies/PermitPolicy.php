<?php

namespace App\Policies;

use App\Models\Permit;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermitPolicy
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

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permit $permit): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if (
            $user->canAny([
                PermissionEnum::PERMIT_READ->value,
                PermissionEnum::PERMIT_READTRASHED->value,
            ])
            && $user->id === $permit->user_id
        )
            return true;

        return false;
    }

    /**
     * @param User $user User from token
     * @param User $userModelBinding User from End Point through Route Model Binding
     */
    public function user(User $user, User $userModelBinding): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if (
            $user->canAny([
                PermissionEnum::PERMIT_READ->value,
                PermissionEnum::PERMIT_READTRASHED->value
            ])
            && $user->id === $userModelBinding->id
        )
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

        if ($user->can(PermissionEnum::PERMIT_CREATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permit $permit): bool
    {
        if ($user->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        if (
            $user->can(PermissionEnum::PERMIT_UPDATE->value)
            && $user->id === $permit->user_id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permit $permit): bool
    {
        if ($user->can(PermissionEnum::KPI_DELETE->value))
            return true;

        if (
            $user->can(PermissionEnum::PERMIT_DELETE->value)
            && $user->id === $permit->user_id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permit $permit): bool
    {
        if ($user->can(PermissionEnum::KPI_RESTORE->value))
            return true;

        if (
            $user->can(PermissionEnum::PERMIT_RESTORE->value)
            && $user->id === $permit->user_id
        )
            return true;

        return false;
    }
}
