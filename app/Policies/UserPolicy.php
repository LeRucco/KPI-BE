<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
    public function view(User $userAuth, User $user): bool
    {
        if ($userAuth->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if (
            $userAuth->canAny([
                PermissionEnum::USER_READ->value,
                PermissionEnum::USER_READTRASHED->value,
            ])
            && $userAuth->id === $user->id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $userAuth): bool
    {
        if ($userAuth->can(PermissionEnum::KPI_CREATE->value))
            return true;

        if ($userAuth->can(PermissionEnum::USER_CREATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $userAuth, User $user): bool
    {
        if ($user->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        if (
            $user->can(PermissionEnum::USER_UPDATE->value)
            && $userAuth->id === $user->id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateImage(User $userAuth, User $user): bool
    {
        if ($userAuth->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        if (
            $userAuth->can(PermissionEnum::USER_UPDATE->value)
            && $userAuth->id === $user->id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updatePassword(User $userAuth, User $user): bool
    {
        if ($userAuth->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        if (
            $userAuth->can(PermissionEnum::USER_UPDATE->value)
            && $userAuth->id === $user->id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $userAuth, User $user): bool
    {
        if ($userAuth->can(PermissionEnum::KPI_DELETE->value))
            return true;

        if (
            $user->can(PermissionEnum::USER_DELETE->value)
            && $userAuth->id === $user->id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $userAuth, User $user): bool
    {
        if ($userAuth->can(PermissionEnum::KPI_RESTORE))
            return true;

        if (
            $user->can(PermissionEnum::USER_RESTORE->value)
            && $userAuth->id === $user->id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $userAuth, User $user): bool
    {
        return false;
    }
}
