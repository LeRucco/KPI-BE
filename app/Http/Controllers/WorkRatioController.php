<?php

namespace App\Http\Controllers;

use App\Models\WorkRatio;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Data\WorkRatio\WorkRatioResponse;
use App\Exceptions\ModelTrashedException;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Data\WorkRatio\WorkRatioCreateRequest;
use App\Data\WorkRatio\WorkRatioUpdateRequest;
use Spatie\LaravelData\PaginatedDataCollection;

class WorkRatioController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'work-ratio';

    public function index()
    {
        Gate::authorize('viewAny', [WorkRatio::class]);

        (array) $data = WorkRatioResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )
            // ->include('work')
            ->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(WorkRatio $workRatio)
    {
        Gate::authorize('view', [$workRatio]);

        (array) $data = WorkRatioResponse::from(
            $workRatio
        )
            ->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function store(WorkRatioCreateRequest $req)
    {
        Gate::authorize('create', [WorkRatio::class]);

        $workRatio = WorkRatio::updateOrCreate(
            ['work_id' => $req->workId],
            $req->only('percentage')->toArray()
        );

        $data = WorkRatioResponse::from(
            $workRatio
        )->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    public function update(WorkRatioUpdateRequest $req, WorkRatio $workRatio)
    {
        Gate::authorize('update', [$workRatio]);

        $isSuccess = $workRatio->update($req->toArray());
        $data = WorkRatioResponse::from(
            $workRatio
        )
            // ->include('work')
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function destroy(WorkRatio $workRatio)
    {
        Gate::authorize('delete', [$workRatio]);

        if ($workRatio->trashed())
            throw ModelTrashedException::alreadySoftDeleted();

        $isSuccess = $workRatio->delete();
        $data = WorkRatioResponse::from(
            $workRatio
        )
            // ->include('work')
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function restore(WorkRatio $workRatio)
    {
        Gate::authorize('restore', [$workRatio]);

        if (!$workRatio->trashed())
            throw ModelTrashedException::stillExist();

        (bool) $isSuccess = $workRatio->restore();

        if ($isSuccess) {
            $data = WorkRatioResponse::from(
                $workRatio
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
            PermissionEnum::WORKRATIO_READTRASHED->value
        ]))
            return WorkRatio::query()->withTrashed();

        return WorkRatio::query();
    }
}
