<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Enums\AttendanceStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\ModelTrashedException;
use App\Exceptions\MyValidationException;
use App\Data\Attendance\AttendanceResponse;
use Spatie\LaravelData\PaginatedDataCollection;
use App\Data\Attendance\AttendanceCreateRequest;
use App\Data\Attendance\AttendanceUpdateRequest;
use App\Data\Attendance\AttendanceUpdateStatusRequest;

class AttendanceController extends Controller
{
    const route = 'attendance';

    public function index()
    {
        Gate::authorize('viewAny', [Attendance::class]);

        (array) $data = AttendanceResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'asc')
                ->paginate(),
            PaginatedDataCollection::class
        )
            // ->include('user')
            ->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(Attendance $attendance)
    {
        Gate::authorize('view', [$attendance]);

        (array) $data = AttendanceResponse::from(
            $attendance
        )
            // ->include('user')
            ->toArray();
        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function user(User $user)
    {
        Gate::authorize('user', [Attendance::class, $user]);

        (array) $data = AttendanceResponse::collect(
            $this->readTrashedOrNot()
                ->where('user_id', '=', $user->id)
                ->orderBy('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function store(AttendanceCreateRequest $req)
    {
        Gate::authorize('create', [Attendance::class]);

        $this->clockValidity($req->clockIn, $req->clockOut);
        $this->statusValidity($req->status);

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        /** @var \App\Models\Attendance */
        $attendance = Attendance::create(array_merge(
            $req->toArray(),
            ['user_id' => $userAuth->id]
        ));

        (array) $data = AttendanceResponse::from(
            $attendance
        )->include('user')->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    public function update(AttendanceUpdateRequest $req, Attendance $attendance)
    {
        Gate::authorize('update', [$attendance]);

        $this->clockValidity($req->clockIn, $req->clockOut);
        $this->clockValidityUpdate($req->clockIn, $req->clockOut, $attendance);
        $this->statusValidity($req->status);

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        (bool) $isSuccess = $attendance->update(array_merge(
            $req->toArray(),
            ['user_id' => $userAuth->id]
        ));
        (array) $data = AttendanceResponse::from(
            $attendance
        )
            // ->include('user')
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function updateStatus(AttendanceUpdateStatusRequest $req, Attendance $attendance)
    {
        Gate::authorize('updateStatus', [$attendance]);

        (bool) $isSuccess = $attendance->update($req->toArray());
        (array) $data = AttendanceResponse::from(
            $attendance
        )
            // ->include('user')
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function destroy(Attendance $attendance)
    {
        Gate::authorize('delete', [$attendance]);

        if ($attendance->trashed())
            throw ModelTrashedException::alreadySoftDeleted();

        (bool) $isSuccess = $attendance->delete();
        (array) $data = AttendanceResponse::from(
            $attendance
        )
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function restore(Attendance $attendance)
    {
        Gate::authorize('restore', [$attendance]);

        if (!$attendance->trashed())
            throw ModelTrashedException::stillExist();

        (bool) $isSuccess = $attendance->restore();

        if ($isSuccess) {
            $data = AttendanceResponse::from(
                $attendance
            )
                // ->include('job')
                ->toArray();

            return $this->success($data, Response::HTTP_OK, 'TODO');
        }

        return $this->error(null, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    private function readTrashedOrNot(): \Illuminate\Database\Eloquent\Builder
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        if ($userAuth->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::ATTENDANCE_READTRASHED->value,
        ]))
            return Attendance::query()->withTrashed();

        return Attendance::query();
    }

    private function clockValidity(?Carbon $clockIn, ?Carbon $clockOut)
    {
        (string) $message = '';
        if ($clockIn && $clockOut)
            $message = 'Both clock in and clock out can not has value';

        if (!$clockIn && !$clockOut)
            $message = 'Both clock in and clock out can not null';

        if (!empty($message))
            throw new MyValidationException($message);
    }

    private function clockValidityUpdate(?Carbon $clockIn, ?Carbon $clockOut, Attendance $attendance)
    {
        (string) $message = '';
        if (empty($clockIn) && empty($attendance->clock_out))
            $message = 'Try to update clock in but data only for clock out';

        if (empty($clockOut) && empty($attendance->clock_in))
            $message = 'Try to update clock out but data only for clock in';

        if (!empty($message))
            throw new MyValidationException($message);
    }

    private function statusValidity(AttendanceStatusEnum $status)
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        if (
            (
                $userAuth->cannot(PermissionEnum::KPI_CREATE->value)
                || $userAuth->cannot(PermissionEnum::KPI_UPDATE->value)
            )
            && $status == AttendanceStatusEnum::APPROVE
        ) {
            $message = 'Only admin can approve attendance';

            throw new MyValidationException($message);
        }
    }
}
