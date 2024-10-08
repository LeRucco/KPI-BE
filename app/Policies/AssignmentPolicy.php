<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentPolicy
{
    use HandlesAuthorization;

    public function before(): bool | null
    {
        /** @var \App\Models\User | null */
        $userAuth = Auth::user();

        if ($userAuth === null) return false;

        if (
            $userAuth->hasRole(RoleEnum::ADMIN->value)
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

    public function viewAnyImages(User $user): bool
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
    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if (
            $user->canAny([
                PermissionEnum::ASSIGNMENT_READ->value,
                PermissionEnum::ASSIGNMENT_READTRASHED->value,
            ])
            && $user->id === $assignment->user_id
        )
            return true;

        return false;
    }

    public function month(User $user): bool
    {
        if ($user->canAny([
            PermissionEnum::ASSIGNMENT_READ->value,
            PermissionEnum::ASSIGNMENT_READTRASHED->value
        ]))
            return true;

        return false;
    }

    public function today(User $user): bool
    {
        if ($user->canAny([
            PermissionEnum::ASSIGNMENT_READ->value,
            PermissionEnum::ASSIGNMENT_READTRASHED->value
        ]))
            return true;

        return false;
    }

    /**
     * Determine whether the user can view the assignment images model
     */
    public function viewImages(User $user, Assignment $assignment): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if (
            $user->canAny([
                PermissionEnum::ASSIGNMENTIMAGE_READ->value,
                PermissionEnum::ASSIGNMENTIMAGE_READTRASHED->value,
            ])
            && $user->id === $assignment->user_id
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
                PermissionEnum::ASSIGNMENT_READ->value,
                PermissionEnum::ASSIGNMENT_READTRASHED->value
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

        if ($user->can(PermissionEnum::ASSIGNMENT_CREATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can create assignment image models.
     */
    public function createImages(User $user): bool
    {
        if ($user->can(PermissionEnum::KPI_CREATE->value))
            return true;

        if ($user->can(PermissionEnum::ASSIGNMENTIMAGE_CREATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Assignment $assignment): bool
    {
        if ($user->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        if (
            $user->can(PermissionEnum::ASSIGNMENT_UPDATE->value)
            && $user->id === $assignment->user_id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        if ($user->can(PermissionEnum::KPI_DELETE->value))
            return true;

        if (
            $user->can(PermissionEnum::ASSIGNMENT_DELETE->value)
            && $user->id === $assignment->user_id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete assignment image model.
     */
    public function deleteImage(User $user, Assignment $assignment): bool
    {
        if ($user->can(PermissionEnum::KPI_DELETE->value))
            return true;

        if (
            $user->can(PermissionEnum::ASSIGNMENTIMAGE_DELETE->value)
            && $user->id === $assignment->user_id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assignment $assignment): bool
    {
        if ($user->can(PermissionEnum::KPI_RESTORE->value))
            return true;

        if (
            $user->can(PermissionEnum::ASSIGNMENT_RESTORE->value)
            && $user->id === $assignment->user_id
        )
            return true;

        return false;
    }
}
