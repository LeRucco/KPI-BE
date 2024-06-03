<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permit;
use Illuminate\Http\Request;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Data\Permit\PermitResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Data\Permit\PermitCreateRequest;
use App\Data\Permit\PermitTodayRequest;
use App\Data\Permit\PermitUpdateRequest;
use App\Exceptions\ModelTrashedException;
use App\Interfaces\ApiBasicReadInterfaces;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\PaginatedDataCollection;

class PermitController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'permit';

    public function index()
    {
        Gate::authorize('viewAny', [Permit::class]);

        (array) $data = PermitResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'asc')
                ->paginate(),
            PaginatedDataCollection::class
        )
            ->include('user')
            ->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(Permit $permit)
    {
        Gate::authorize('view', [Permit::class, $permit]);

        (array) $data = PermitResponse::from(
            $permit
        )
            ->include('user')
            ->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function today(PermitTodayRequest $req)
    {
        $date = $req->date->format('Y-m-d');

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        $result = DB::table('permits')
            ->whereDate('date', '=', $date)
            ->where('user_id', '=', $userAuth->id)
            ->first(['*']);

        // $result = Permit::whereDate('date', '=', $date)
        //     ->first(['*']);

        if ($result == null)
            return $this->success(null, Response::HTTP_OK, 'TODO');

        (array) $data = PermitResponse::from(
            $result
        )
            // ->include('user')
            ->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function user(User $user)
    {
        Gate::authorize('user', [Permit::class, $user]);

        (array) $data = PermitResponse::collect(
            $this->readTrashedOrNot()
                ->where('user_id', '=', $user->id)
                ->orderBY('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function store(PermitCreateRequest $req)
    {
        Gate::authorize('create', [Permit::class]);

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        /** @var \App\Models\Permit */
        $permit = Permit::create(array_merge(
            $req->toArray(),
            ['user_id' => $userAuth->id]
        ));

        if ($req->images !== null)
            foreach ($req->images as $index => $uploadedFile) {
                $permit
                    ->addMedia($uploadedFile)
                    ->usingName($permit->id . '-' . $permit->user_id . '-' . $permit->type . '-' . $index)
                    ->toMediaCollection(Permit::IMAGE);
            }

        (array) $data = PermitResponse::from(
            $permit
        )
            // ->include('user')
            ->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    public function update(PermitUpdateRequest $req, Permit $permit)
    {
        Gate::authorize('update', [Permit::class, $permit]);

        (bool) $isSuccess = $permit->update(
            $req->toArray()
        );

        (array) $data = PermitResponse::from(
            $permit
        )
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function destroy(Permit $permit)
    {
        Gate::authorize('delete', [Permit::class, $permit]);

        if ($permit->trashed())
            throw ModelTrashedException::alreadySoftDeleted();

        $isSuccess = $permit->delete();

        (array) $data = PermitResponse::from(
            $permit
        )
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function restore(Permit $permit)
    {
        Gate::authorize('restore', [Permit::class, $permit]);

        if (!$permit->trashed())
            throw ModelTrashedException::stillExist();

        (bool) $isSuccess = $permit->restore();

        if ($isSuccess) {
            (array) $data = PermitResponse::from(
                $permit
            )->toArray();

            return $this->success($data, Response::HTTP_OK, 'TODO');
        }

        return $this->error(null, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function readTrashedOrNot(): \Illuminate\Database\Eloquent\Builder
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        if ($userAuth->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::PERMIT_READTRASHED->value
        ]))
            return Permit::query()->withTrashed();

        return Permit::query();
    }
}
