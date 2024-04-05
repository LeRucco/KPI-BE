<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function before(): bool | null
    {
        /** @var \App\Models\User | null */
        $user = Auth::user();

        if ($user === null) return false;

        if (
            $user->hasRole(RoleEnum::ADMIN->value)
            || $user->hasRole(RoleEnum::DEVELOPER->value)
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

        // if ($user->can(PermissionEnum::KPI_READ->value))
        //     return true;

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->canAny([
            PermissionEnum::KPI_READ->value,
            PermissionEnum::KPI_READTRASHED->value
        ]))
            return true;

        if (
            $user->can(PermissionEnum::ATTENDANCE_READ->value)
            && $user->id === $attendance->user_id
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

        if ($user->can(PermissionEnum::ATTENDANCE_CREATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        if (
            $user->can(PermissionEnum::ATTENDANCE_UPDATE->value)
            && $user->id === $attendance->user_id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can update the status model.
     */
    public function updateStatus(User $user, Attendance $attendance): bool
    {
        if ($user->can(PermissionEnum::KPI_UPDATE->value))
            return true;

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        if ($user->can(PermissionEnum::KPI_DELETE->value))
            return true;

        if (
            $user->can(PermissionEnum::ATTENDANCE_DELETE->value)
            && $user->id === $attendance->user_id
        )
            return true;

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        if ($user->can(PermissionEnum::KPI_RESTORE))
            return true;

        if (
            $user->can(PermissionEnum::ATTENDANCE_RESTORE->value)
            && $user->id === $attendance->user_id
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
            $user->can(PermissionEnum::ATTENDANCE_READ->value)
            && $user->id === $userModelBinding->id
        )
            return true;

        return false;
    }
}