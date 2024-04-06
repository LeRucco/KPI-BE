<?php

namespace App\Http\Controllers;

use App\Data\Work\WorkCreateRequest;
use App\Models\Work;
use Illuminate\Http\Request;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Data\Work\WorkResponse;
use App\Data\Work\WorkUpdateRequest;
use App\Exceptions\ModelTrashedException;
use App\Interfaces\ApiBasicReadInterfaces;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\PaginatedDataCollection;

class WorkController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'work';

    public function index()
    {
        Gate::authorize('viewAny', [Work::class]);

        (array) $data = WorkResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'asc')
                ->paginate(),
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(Work $work)
    {
        Gate::authorize('view', [$work]);

        (array) $data = WorkResponse::from(
            $work
        )
            ->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function store(WorkCreateRequest $req)
    {
        Gate::authorize('create', [Work::class]);

        /** @var \App\Models\Work */
        $work = Work::create($req->toArray());

        (array) $data = WorkResponse::from(
            $work
        )->toArray();
        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    public function update(WorkUpdateRequest $req, Work $work)
    {
        (bool) $isSuccess = $work->update($req->toArray());
        (array) $data = WorkResponse::from(
            $work
        )->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function destroy(Work $work)
    {
        Gate::authorize('delete', [$work]);

        if ($work->trashed())
            throw ModelTrashedException::alreadySoftDeleted();

        (bool) $isSuccess = $work->delete();
        (array) $data = WorkResponse::from(
            $work
        )
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function restore(Work $work)
    {
        Gate::authorize('restore', [$work]);

        if (!$work->trashed())
            throw ModelTrashedException::stillExist();

        (bool) $isSuccess = $work->restore();

        if ($isSuccess) {
            $data = WorkResponse::from(
                $work
            )
                ->toArray();

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
            PermissionEnum::WORK_READTRASHED->value
        ]))
            return Work::query()->withTrashed();

        return Work::query();
    }
}
