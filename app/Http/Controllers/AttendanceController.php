<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Enums\AttendanceStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\ModelTrashedException;
use App\Exceptions\MyValidationException;
use App\Data\Attendance\AttendanceResponse;
use Spatie\LaravelData\PaginatedDataCollection;
use App\Data\Attendance\AttendanceCreateRequest;
use App\Data\Attendance\AttendanceTotalRequest;
use App\Data\Attendance\AttendanceUpdateRequest;
use App\Data\Attendance\AttendanceUpdateStatusRequest;
use App\Enums\RoleEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public function total(AttendanceTotalRequest $req)
    {
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
            RoleEnum::DEVELOPER->value
        ])->get();
        return $users;

        $totalAlpha = $users->count() - $totalAttend;

        return [$totalAttend, $totalLate, $totalEarlyLeave, $totalAlpha];

        // Attendance::all()->where('clock_in', '=', $date)

        return '';
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
}
