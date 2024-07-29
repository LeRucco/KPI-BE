<?php

namespace App\Http\Controllers;

use App\Data\Permit\PermitCheckRequest;
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
use App\Data\Permit\PermitTotalAdminRequest;
use App\Data\Permit\PermitUpdateRequest;
use App\Data\Permit\PermitUpdateStatusRequest;
use App\Enums\RoleEnum;
use App\Exceptions\ModelTrashedException;
use App\Interfaces\ApiBasicReadInterfaces;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\DataCollection;
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

    public function check(PermitCheckRequest $req)
    {
        Gate::authorize('check', [Permit::class]);

        $date = $req->date->format('Y-m-d');
        $type = $req->type == null ? null : $req->type->value;
        $status = $req->status == null ? null : $req->status->value;
        $userId = $req->userId;

        $result = DB::table('permits')
            ->join('users', 'permits.user_id', '=', 'users.id')
            ->whereDate('permits.date', '=', $date)
            // ->when(function (Builder $query) use ($date) {
            //     $query->whereDate('permits.date', '=', $date);
            // })
            ->when($type, function (Builder $query, int $type) {
                $query->where('permits.type', '=', $type);
            })
            ->when($status, function (Builder $query, int $status) {
                $query->where('permits.status', '=', $status);
            })
            ->when($userId, function (Builder $query, string $userId) {
                $query->where('permits.user_id', '=', $userId);
            })
            ->select(['permits.*', 'users.full_name'])
            ->get();

        (array) $data = PermitResponse::collect(
            $result->toArray(),
            DataCollection::class
        )->toArray();

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

    public function updateStatus(PermitUpdateStatusRequest $req, Permit $permit)
    {
        Gate::authorize('updateStatus', [Permit::class, $permit]);

        (bool) $isSuccess = $permit->update($req->toArray());
        (array) $data = PermitResponse::from(
            $permit
        )
            // ->include('user')
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function totalAdminPermit(PermitTotalAdminRequest $req)
    {
        // TODO Policy
        $selectedMonthYear = $req->date->format('Y-m'); // yyyy-MM
        $fromDate = $req->date->format('Y-m') . '-01';    // First Date of the Month
        $toDate = $req->date->format('Y-m-d');        // Selected Date / Today Date
        $clockInLimit = '08:00:00';
        $clockOutLimit = '17:00:00';

        $totalAttend = DB::scalar('
        select COUNT(*)
            from (
                select
                    DATE(combine_clock.clock) as clock
                    , COUNT(*) as clockaa
                from (
                    select
                        IFNULL(clock_in, clock_out) as clock
                    from attendances a
                    where
                        a.status != 3
                        and
                        (
                            DATE_FORMAT(a.clock_in, "%Y-%m") = :selected_month_year1 /* STUPID PDO PHP Limitation*/
                            or
                            DATE_FORMAT(a.clock_out, "%Y-%m") = :selected_month_year2 /* STUPID PDO PHP Limitation*/
                        )
                        and
                        (
                            TIME_FORMAT(a.clock_in, "%T") <= :clock_in_limit
                            or
                            TIME_FORMAT(a.clock_out, "%T") >= :clock_out_limit
                        )
                ) as combine_clock
                group by (DATE(clock))
                having COUNT(*) >= 2
            ) as rowitems
        ', [
            'selected_month_year1' => $selectedMonthYear,
            'selected_month_year2' => $selectedMonthYear,
            'clock_in_limit' => $clockInLimit,
            'clock_out_limit' => $clockOutLimit,
        ]);

        $totalSickOrLeave = DB::scalar('
        select COUNT(*) as rowitems
        from permits p
        where 1 = 1
            and DATE_FORMAT(p.date, "%Y-%m") = :selected_month_year
            and p.type in (1,3) /* 1 = Sick/Sakit, 3 = Leave/Izin */
            and p.status = 1 /* 2 = approved */
        ', [
            'selected_month_year' => $selectedMonthYear,
        ]);

        $totalPaidLeave = DB::scalar('
        select COUNT(*) as rowitems
        from permits p
        where 1 = 1
            and DATE_FORMAT(p.date, "%Y-%m") = :selected_month_year
            and p.type = 2 /* 2 = Paid Leave/Cuti */
            and p.status = 2 /* 2 = approved */
        ', [
            'selected_month_year' => $selectedMonthYear,
        ]);

        /** @var Collection */
        $users = User::withoutRole([
            RoleEnum::SUPER_ADMIN->value,
            RoleEnum::ADMIN->value,
            RoleEnum::DEVELOPER->value
        ])->get();

        $totalAlpha = $this->number_of_working_days($fromDate, $toDate) - $totalAttend;

        (array) $data = [$totalSickOrLeave, $totalPaidLeave, $totalAlpha];

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    private function number_of_working_days(String $from, String $to)
    {
        $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)
        // $holidayDays = ['*-12-25', '*-01-01', '2013-12-23']; # variable and fixed holidays
        $holidayDays = []; // TODO Hari libur nya mau hari apa aja ???

        $from = new DateTime($from);
        $to = new DateTime($to);
        $to->modify('+1 day');
        $interval = new DateInterval('P1D');
        $periods = new DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if (!in_array($period->format('N'), $workingDays)) continue;
            if (in_array($period->format('Y-m-d'), $holidayDays)) continue;
            if (in_array($period->format('*-m-d'), $holidayDays)) continue;
            $days++;
        }
        return $days;
    }
}
