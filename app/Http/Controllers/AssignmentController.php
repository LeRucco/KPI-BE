<?php

namespace App\Http\Controllers;

use App\Data\Assignment\AssignmentCheckRequest;
use App\Data\Assignment\AssignmentCreateRequest;
use App\Data\Assignment\AssignmentMonthRequest;
use App\Data\Assignment\AssignmentResponse;
use App\Data\Assignment\AssignmentTodayRequest;
use App\Data\Assignment\AssignmentUpdateRequest;
use Illuminate\Http\Response;
use App\Enums\PermissionEnum;
use App\Exceptions\ModelTrashedException;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\DataCollection;
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

    public function check(AssignmentCheckRequest $req)
    {
        Gate::authorize('viewAny', [Assignment::class]);

        $date = $req->date->format('Y-m-d');
        $userId = $req->userId;

        $result = DB::table('assignments')
            ->whereDate('assignments.date', '=', $date)
            ->when($userId, function (Builder $query, string $userId) {
                $query->where('assignments.user_id', '=', $userId);
            })
            // ->select(['assignments.*'])
            // ->get()
            ->paginate($perPage = 3);

        (array) $data = AssignmentResponse::collect(
            $result,
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function month(AssignmentMonthRequest $req)
    {
        Gate::authorize('month', [Assignment::class]);

        $date = $req->date->format('Y-m');

        /** @var \App\Models\User */
        $userAuthId = Auth::user()->id;

        $result = DB::table('assignments')
            ->where('user_id', $userAuthId)
            ->where(DB::raw("DATE_FORMAT(date, '%Y-%m')"), $date)
            ->paginate($perPage = 3);

        (array) $data = AssignmentResponse::collect(
            $result,
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function today(AssignmentTodayRequest $req)
    {
        Gate::authorize('today', [Assignment::class]);

        $date = $req->date->format('Y-m-d');

        /** @var \App\Models\User */
        $userAuthId = Auth::user()->id;

        $result = DB::table('assignments')
            ->whereDate('assignments.date', '=', $date)
            ->where('assignments.user_id', '=', $userAuthId)
            ->get();

        (array) $data = AssignmentResponse::collect(
            $result,
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    function show(Assignment $assignment)
    {
        Gate::authorize('view', [Assignment::class, $assignment]);

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
        Gate::authorize('createImages', [Assignment::class]);

        // TODO Database Transaction
        /**
         * Jika Assignment berhasil create, tapi addMedia images nya gagal,
         * maka rollback Assignment Create nya
         * */


        /** @var \App\Models\User */
        $userAuth = Auth::user();

        /** @var \App\Models\Assignment */
        $assignment = Assignment::create(array_merge(
            $req->except('images')->toArray(),
            ['user_id' => $userAuth->id]
        ));

        if ($req->images !== null)
            foreach ($req->images as $index => $uploadedFile) {
                $assignment
                    ->addMedia($uploadedFile)
                    ->usingName($assignment->id . '-' . $assignment->user_id . '-' . $assignment->work_id . '-' . $index)
                    ->toMediaCollection(Assignment::IMAGE);
            }

        (array) $data = AssignmentResponse::from(
            $assignment
        )
            ->include('')
            ->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    function update(AssignmentUpdateRequest $req, Assignment $assignment)
    {
        Gate::authorize('update', [Assignment::class, $assignment]);

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
        Gate::authorize('delete', [Assignment::class, $assignment]);

        if ($assignment->trashed())
            throw ModelTrashedException::alreadySoftDeleted();

        $isSuccess = $assignment->delete();
        (array) $data = AssignmentResponse::from(
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
        Gate::authorize('restore', [Assignment::class, $assignment]);

        if (!$assignment->trashed())
            throw ModelTrashedException::stillExist();

        (bool) $isSuccess = $assignment->restore();

        if ($isSuccess) {
            (array) $data = AssignmentResponse::from(
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
