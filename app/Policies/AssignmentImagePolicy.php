<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use App\Models\AssignmentImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentImagePolicy
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
    public function view(User $user, AssignmentImage $assignmentImage): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if ($user->canAny([
            PermissionEnum::ASSIGNMENTIMAGE_READ->value,
            PermissionEnum::ASSIGNMENTIMAGE_READTRASHED->value,
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

        if ($user->can(PermissionEnum::ASSIGNMENTIMAGE_CREATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssignmentImage $assignmentImage): bool
    {
        return false;
    }
}
