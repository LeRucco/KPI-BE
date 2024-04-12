<?php

namespace App\Http\Controllers;

use App\Data\Assignment\AssignmentCreateRequest;
use App\Data\Assignment\AssignmentResponse;
use App\Data\Assignment\AssignmentUpdateRequest;
use Illuminate\Http\Response;
use App\Enums\PermissionEnum;
use App\Exceptions\ModelTrashedException;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\PaginatedDataCollection;

class AssignmentController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'assignment';

    public function index()
    {
        Gate::authorize('viewAny', [Assignment::class]);

        (array) $data = AssignmentResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )
            ->include('user', 'work', null ?: '')
            ->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    function show(Assignment $assignment)
    {
        Gate::authorize('view', [$assignment]);

        (array) $data = AssignmentResponse::from(
            $assignment
        )
            ->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    function user(User $user)
    {
        Gate::authorize('user', [Assignment::class, $user]);

        (array) $data = AssignmentResponse::collect(
            $this->readTrashedOrNot()
                ->where('user_id', '=', $user->id)
                ->orderBy('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )
            ->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    function store(AssignmentCreateRequest $req)
    {
        Gate::authorize('create', [Assignment::class]);

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        /** @var \App\Models\Assignment */
        $assignment = Assignment::create(array_merge(
            $req->toArray(),
            ['user_id' => $userAuth->id]
        ));

        (array) $data = AssignmentResponse::from(
            $assignment
        )
            ->include('')
            ->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    function update(AssignmentUpdateRequest $req, Assignment $assignment)
    {
        Gate::authorize('update', [$assignment]);

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        (bool) $isSuccess = $assignment->update(array_merge(
            $req->toArray(),
            ['user_id' => $userAuth->id]
        ));
        (array) $data = AssignmentResponse::from(
            $assignment
        )
            ->include('')
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function destroy(Assignment $assignment)
    {
        Gate::authorize('delete', [$assignment]);

        if ($assignment->trashed())
            throw ModelTrashedException::alreadySoftDeleted();

        $isSuccess = $assignment->delete();
        $data = AssignmentResponse::from(
            $assignment
        )
            ->include('')
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function restore(Assignment $assignment)
    {
        Gate::authorize('restore', [$assignment]);

        if (!$assignment->trashed())
            throw ModelTrashedException::stillExist();

        (bool) $isSuccess = $assignment->restore();

        if ($isSuccess) {
            $data = AssignmentResponse::from(
                $assignment
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
            PermissionEnum::ASSIGNMENT_READTRASHED->value
        ]))
            return Assignment::query()->withTrashed();

        return Assignment::query();
    }
}
