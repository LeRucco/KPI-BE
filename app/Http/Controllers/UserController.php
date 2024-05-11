<?php

namespace App\Http\Controllers;

use App\Data\User\UserCreateRequest;
use App\Models\User;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Data\User\UserResponse;
use App\Data\User\UserUpdateImageRequest;
use App\Data\User\UserUpdateRequest;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ApiBasicReadInterfaces;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class UserController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'user';

    public function index()
    {
        (array) $data = UserResponse::collect(
            User::paginate(),
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(User $user)
    {
        // return $user;
        (array) $data = UserResponse::from(
            $user
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function store(UserCreateRequest $req)
    {
        /** @var App\Models\User */
        $user = User::create($req->toArray());

        (array) $data = UserResponse::from(
            $user
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function update(UserUpdateRequest $req, User $user)
    {
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
        $user = User::findOrFail($req->userId)->first();

        if ($req->image !== null) {
            $user
                ->addMedia($req->image)
                ->usingName($user->id . '-' . $user->nrp)
                ->toMediaCollection(User::IMAGE);
        }
    }

    public function destroy(User $user)
    {
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
