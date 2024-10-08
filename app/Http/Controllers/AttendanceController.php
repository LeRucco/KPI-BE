<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Enums\AttendanceStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Query\Builder;
use Spatie\LaravelData\DataCollection;
use App\Exceptions\ModelTrashedException;
use App\Exceptions\MyValidationException;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Data\Attendance\AttendanceResponse;
use App\Data\Attendance\AttendanceCheckRequest;
use Spatie\LaravelData\PaginatedDataCollection;
use App\Data\Attendance\AttendanceCreateRequest;
use App\Data\Attendance\AttendanceTodayRequest;
use App\Data\Attendance\AttendanceTotalAdminRequest;
use App\Data\Attendance\AttendanceUpdateRequest;
use App\Data\Attendance\AttendanceUpdateStatusRequest;
use App\Data\AttendancePermit\AttendancePermitTotalAdminRequest;

class AttendanceController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'attendance';

    public function index()
    {
        Gate::authorize('viewAny', [Attendance::class]);

        (array) $data = AttendanceResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )
            // ->include('user')
            ->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function check(AttendanceCheckRequest $req)
    {
        Gate::authorize('check', [Attendance::class]);

        $date = $req->date->format('Y-m-d');
        $status = $req->status == null ? null : $req->status->value;
        $userId = $req->userId;

        $result = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->where(function (Builder $query) use ($date) {
                $query->whereDate('attendances.clock_in', '=', $date)
                    ->orWhereDate('attendances.clock_out', '=', $date);
            })
            ->when($status, function (Builder $query, int $status) {
                $query->where('attendances.status', '=', $status);
            })
            ->when($userId, function (Builder $query, string $userId) {
                $query->where('attendances.user_id', '=', $userId);
            })
            ->select(['attendances.*', 'users.full_name'])
            ->get();

        (array) $data = AttendanceResponse::collect(
            $result->toArray(),
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function today(AttendanceTodayRequest $req)
    {
        Gate::authorize('today', [Attendance::class]);

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        $date = $req->date->format('Y-m-d');

        $result = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->where(function (Builder $query) use ($date) {
                $query->whereDate('attendances.clock_in', '=', $date)
                    ->orWhereDate('attendances.clock_out', '=', $date);
            })
            ->where('attendances.user_id', '=', $userAuth->id)
            ->orderBy('attendances.id', 'asc')
            ->select(['attendances.*', 'users.full_name'])
            ->get();

        (array) $data = AttendanceResponse::collect(
            $result->toArray(),
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function show(Attendance $attendance)
    {
        Gate::authorize('view', [Attendance::class, $attendance]);

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
        )->include('')->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    public function update(AttendanceUpdateRequest $req, Attendance $attendance)
    {
        Gate::authorize('update', [Attendance::class, $attendance]);

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
        Gate::authorize('updateStatus', [Attendance::class, $attendance]);

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
        Gate::authorize('delete', [Attendance::class, $attendance]);

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
        Gate::authorize('restore', [Attendance::class, $attendance]);

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

    public function readTrashedOrNot(): \Illuminate\Database\Eloquent\Builder
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

    public function totalAdminAttendance(AttendancePermitTotalAdminRequest $req)
    {
        // TODO Policy

        $date = $req->date->format('Y-m-d');

        $clockInDown = "$date 06:00:00";
        $clockInUp = "$date 08:00:00";
        $clockOutDown = "$date 17:00:00";
        $clockOutUp = "$date 23:59:59";

        $totalAttend = DB::scalar("
        select
            count(user_id)
        from (
            select
                user_id , COUNT(*)
            from attendances a
            where
                status != 3
                and
                (
                    clock_in between :clock_in_down and :clock_in_up
                    or
                    clock_out between :clock_out_down and :clock_out_up
                )
            group by (user_id)
            having count(*) >= 2
        ) as rowitems;
        ", [
            'clock_in_down' => $clockInDown,
            'clock_in_up' => $clockInUp,
            'clock_out_down' => $clockOutDown,
            'clock_out_up' => $clockOutUp
        ]);

        $totalLate = DB::scalar("
        select
            COUNT(user_id)
        from attendances a
        where
            status != 3
            and
            clock_in > :clock_in_up
            and
            clock_in < :clock_out_down
        ", [
            'clock_in_up' => $clockInUp,
            'clock_out_down' => $clockOutDown
        ]);

        $totalEarlyLeave = DB::scalar("
        select
            COUNT(user_id)
        from attendances a
        where
            status != 3
            and
            clock_out > :clock_in_up
            and
            clock_out < :clock_out_down
        ", [
            'clock_in_up' => $clockInUp,
            'clock_out_down' => $clockOutDown
        ]);

        /** @var Collection */
        $users = User::withoutRole([
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::ADMIN->value,
        ])->get();

        $totalAlpha = $users->count() - $totalAttend;

        (array) $data = [$totalAttend, $totalLate, $totalEarlyLeave, $totalAlpha];

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }
}
