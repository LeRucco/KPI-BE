<?php

namespace App\Http\Controllers;

use App\Data\User\UserCreateRequest;
use App\Data\User\UserDropdownResponse;
use App\Models\User;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Data\User\UserResponse;
use App\Data\User\UserUpdateImageRequest;
use App\Data\User\UserUpdatePasswordRequest;
use App\Data\User\UserUpdateRequest;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ApiBasicReadInterfaces;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'user';

    public function index()
    {
        Gate::authorize('viewAny', [User::class]);

        (array) $data = UserResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'asc')
                ->paginate(),
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(User $user)
    {
        Gate::authorize('view', [User::class, $user]);
        (array) $data = UserResponse::from(
            $user
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function attendanceDropdown()
    {
        Gate::authorize('viewAny', [User::class]);
        (array) $data = UserDropdownResponse::collect(
            $this->readTrashedOrNot()
                ->withoutRole([RoleEnum::SUPER_ADMIN->value, RoleEnum::ADMIN->value, RoleEnum::DEVELOPER->value])
                ->orderBy('id', 'asc')
                ->get(['id', 'nrp', 'full_name']),
            DataCollection::class,
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function store(UserCreateRequest $req)
    {
        Gate::authorize('create', [User::class]);

        /** @var App\Models\User */
        $user = User::create($req->toArray());

        (array) $data = UserResponse::from(
            $user
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function update(UserUpdateRequest $req, User $user)
    {
        Gate::authorize('update', [User::class, $user]);
        (bool) $isSuccess = $user->update($req->toArray());

        (array) $data = UserResponse::from(
            $user
        )->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function updateImage(UserUpdateImageRequest $req)
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        Gate::authorize('updateImage', [User::class, $userAuth]);

        if ($req->image !== null) {
            $userAuth
                ->addMedia($req->image)
                ->usingName($userAuth->id . '-' . $userAuth->nrp)
                ->toMediaCollection(User::IMAGE);
        }

        (array) $data = UserResponse::from(
            $userAuth
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function updatePassword(UserUpdatePasswordRequest $req)
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        Gate::authorize('updatePassword', [User::class, $userAuth]);

        // return $req;
        // return Hash::make($req->password);

        (bool) $isSuccess = $userAuth->update([
            'password' => Hash::make($req->password)
        ]);

        (array) $data = UserResponse::from(
            $userAuth
        )->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }


    // TODO Soft Deleted and Restore not yet testing and implemented.
    public function destroy(User $user)
    {

        Gate::authorize('delete', [User::class, $user]);

        // Using Soft Delete
        $isSuccess = $user->delete();
        if ($isSuccess)
            return $this->success([], Response::HTTP_OK, 'TODO');

        return $this->error([], Response::HTTP_OK, 'TODO');
    }

    public function readTrashedOrNot(): \Illuminate\Database\Eloquent\Builder
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        if ($userAuth->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::ATTENDANCE_READTRASHED->value,
        ]))
            return User::query()->withTrashed();

        return User::query();
    }
}
