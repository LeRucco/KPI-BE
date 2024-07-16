<?php

namespace App\Http\Controllers;

use App\Data\Attendance\AttendanceExportRequest;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Enums\PermitTypeEnum;
use Illuminate\Http\Response;
use App\Enums\CalenderColorEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\AttendanceStatusEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Query\Builder;
use Spatie\LaravelData\DataCollection;
use App\Enums\AttendancePermitSourceEnum;
use App\Data\AttendancePermit\AttendancePermitMonthRequest;
use App\Data\AttendancePermit\AttendancePermitMonthResponse;
use App\Data\AttendancePermit\AttendancePermitTotalEmpRequest;
use App\Data\AttendancePermit\AttendancePermitDetailDateRequest;
use App\Data\AttendancePermit\AttendancePermitTotalAdminRequest;
use App\Data\AttendancePermit\AttendancePermitDetailDateResponse;
use App\Exports\AttendanceExport;

class AttendancePermitController extends Controller
{
    const route = 'attendance-permit';

    public function detailDate(AttendancePermitDetailDateRequest $req)
    {
        // TODO Policy
        $date = $req->date->format('Y-m-d');        // Selected Date
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        /** @var \Illuminate\Support\Collection */
        $result = collect();

        if ($req->source == AttendancePermitSourceEnum::ATTENDANCE) {
            $result = DB::table('attendances')
                ->where('user_id', '=', $userAuth->id)
                ->where(function (Builder $query) use ($date) {
                    $query->whereDate('clock_in', '=', $date)
                        ->orWhereDate('clock_out', '=', $date);
                })
                ->orderBy('id', 'asc')
                ->selectRaw('
                    ? as source
                    , clock_in as date1
                    , clock_out as date2
                    , status
                    , NULL as type
                    , case
                        when clock_in is not null AND status != ? then ?
                        when clock_in is not null AND status = ? then ?
                        when clock_out is not null AND status != ? then ?
                        when clock_out is not null AND status = ? then ?
                        else NULL
                    end as color

                ', [
                    AttendancePermitSourceEnum::ATTENDANCE->value,
                    AttendanceStatusEnum::REJECT->value, CalenderColorEnum::ATTEND->value,
                    AttendanceStatusEnum::REJECT->value, CalenderColorEnum::LATE->value,
                    AttendanceStatusEnum::REJECT->value, CalenderColorEnum::ATTEND->value,
                    AttendanceStatusEnum::REJECT->value, CalenderColorEnum::EARLY_LEAVE->value,
                ])
                ->get();
        } else if ($req->source == AttendancePermitSourceEnum::PERMIT) {
            $result = DB::table('permits')
                ->where('user_id', '=', $userAuth->id)
                ->whereDate('date', '=', $date)
                ->selectRaw('
                    ? as source
                    , date as date1
                    , NULL as date2
                    , status
                    , type
                    , case
                        when type = ? then ?
                        when type = ? then ?
                        when type = ? then ?
                        else NULL
                    end as color
                ', [
                    AttendancePermitSourceEnum::PERMIT->value,
                    PermitTypeEnum::SICK->value, CalenderColorEnum::SICK_OR_LEAVE,
                    PermitTypeEnum::PAID_LEAVE->value, CalenderColorEnum::PAID_LEAVE,
                    PermitTypeEnum::LEAVE->value, CalenderColorEnum::SICK_OR_LEAVE,
                ])
                ->get();
        }
        // return $result->toArray();
        (array) $data = AttendancePermitDetailDateResponse::collect(
            $result->toArray(),
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function totalEmp(AttendancePermitTotalEmpRequest $req)
    {
        // TODO Policy
        $selectedMonthYear = $req->date->format('Y-m'); // yyyy-MM
        $fromDate = $req->date->format('Y-m') . '-01';    // First Date of the Month
        $toDate = $req->date->format('Y-m-d');        // Selected Date / Today Date
        $clockInLimit = '08:00:00';
        $clockOutLimit = '17:00:00';

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        $userId = $userAuth->id;
        // return [$userId, $selectedMonthYear, $fromDate, $toDate, $clockInLimit, $clockOutLimit];

        // TODO Use Query Builder for future development --Le Rucco
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
                    where 1 = 1
                        and a.user_id = :user_idz
                        and a.status != 3
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
            'user_idz' => $userId,
            'selected_month_year1' => $selectedMonthYear,
            'selected_month_year2' => $selectedMonthYear,
            'clock_in_limit' => $clockInLimit,
            'clock_out_limit' => $clockOutLimit,
        ]);
        // return $totalAttend;

        $totalLateCheckIn = DB::scalar('
        select COUNT(*) as rowitems
            from (
                select *
                from attendances a
                where 1 = 1
                    and a.user_id = :user_id2
                    and a.status != 3
                    and
                    (
                        DATE_FORMAT(a.clock_in, "%Y-%m") = :selected_month_year1
                        or
                        DATE_FORMAT(a.clock_out, "%Y-%m") = :selected_month_year2
                    )
                    and TIME_FORMAT(a.clock_in, "%T") > :clock_in_limit
            ) as rowitems
        ', [
            'user_id2' => $userId,
            'selected_month_year1' => $selectedMonthYear,
            'selected_month_year2' => $selectedMonthYear,
            'clock_in_limit' => $clockInLimit
        ]);

        $totalEarlyCheckOut = DB::scalar('
        select COUNT(*) as rowitems
        from (
            select *
            from attendances a
            where 1 = 1
                and a.user_id = :user_id2
                and a.status != 3
                and
                (
                    DATE_FORMAT(a.clock_in, "%Y-%m") = :selected_month_year1
                    or
                    DATE_FORMAT(a.clock_out, "%Y-%m") = :selected_month_year2
                )
                and TIME_FORMAT(a.clock_out, "%T") < :clock_out_limit
        ) as rowitems
        ', [
            'user_id2' => $userId,
            'selected_month_year1' => $selectedMonthYear,
            'selected_month_year2' => $selectedMonthYear,
            'clock_out_limit' => $clockOutLimit
        ]);

        $totalSickOrLeave = DB::scalar('
        select COUNT(*) as rowitems
        from permits p
        where 1 = 1
            and p.user_id = :user_id2
            and DATE_FORMAT(p.date, "%Y-%m") = :selected_month_year
            and p.type in (1,3) /* 1 = Sick/Sakit, 3 = Leave/Izin */
            and p.status = 2 /* 2 = approved */
        ', [
            'user_id2' => $userId,
            'selected_month_year' => $selectedMonthYear,
        ]);

        $totalPaidLeave = DB::scalar('
        select COUNT(*) as rowitems
        from permits p
        where 1 = 1
            and p.user_id = :user_id2
            and DATE_FORMAT(p.date, "%Y-%m") = :selected_month_year
            and p.type = 2 /* 2 = Paid Leave/Cuti */
            and p.status = 2 /* 2 = approved */
        ', [
            'user_id2' => $userId,
            'selected_month_year' => $selectedMonthYear,
        ]);

        $totalAlpha = $this->number_of_working_days($fromDate, $toDate) - $totalAttend - $totalSickOrLeave - $totalPaidLeave;

        (array) $data = [$totalAttend, $totalLateCheckIn, $totalEarlyCheckOut, $totalAlpha, $totalSickOrLeave, $totalPaidLeave];

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function totalAdmin(AttendancePermitTotalAdminRequest $req)
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
            RoleEnum::DEVELOPER->value
        ])->get();

        $totalAlpha = $users->count() - $totalAttend;

        (array) $data = [$totalAttend, $totalLate, $totalEarlyLeave, $totalAlpha];

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function month(AttendancePermitMonthRequest $req)
    {
        // TODO policy

        $selectedMonthYear = $req->date->format('Y-m'); // yyyy-MM

        /// Attendance
        $colorSuccess = CalenderColorEnum::ATTEND->value; // Success clock in and clock out
        $colorLate = CalenderColorEnum::LATE->value;
        $colorEarlyLeave = CalenderColorEnum::EARLY_LEAVE->value;

        /// Permit
        $colorSick = CalenderColorEnum::SICK_OR_LEAVE->value;
        $colorLeave = CalenderColorEnum::SICK_OR_LEAVE->value;
        $colorPaidLeave = CalenderColorEnum::PAID_LEAVE->value;

        $permitTypeSick = PermitTypeEnum::SICK->value;
        $permitTypePaidLeave = PermitTypeEnum::PAID_LEAVE->value;
        $permitTypeLeave = PermitTypeEnum::LEAVE->value;

        /** @var \App\Models\User */
        $userAuth = Auth::user();

        $result = DB::select("
        select *
        from (
            select
                :source_attendance as source
                , final_attendance.*
            from (
                select
                    DATE(combine_clock.clock) as date
                    , group_concat(combine_clock.color1) as color1
                    , group_concat(combine_clock.color2) as color2
                -- 	, COUNT(*) as clockaa
                from (
                    select
                        IFNULL(a.clock_in, a.clock_out) as clock
                        , case
                            when a.clock_in is not null AND a.status != :status1 then :color_success1
                            when a.clock_in is not null AND a.status = :status2 then :color_failed1
                            else NULL
                        end as color1
                        , case
                            when a.clock_out  is not null AND a.status != :status3 then :color_success2
                            when a.clock_out is not null AND a.status = :status4 then :color_failed2
                            else null
                        end as color2
                        , a.*
                    from attendances a
                    where 1 = 1
                        and a.user_id = :user_id1
                        and
                        (
                            DATE_FORMAT(a.clock_in, '%Y-%m') = :selected_month_year1
                            or
                            DATE_FORMAT(a.clock_out, '%Y-%m') = :selected_month_year2
                        )
                ) as combine_clock
                group by DATE(clock)
                having COUNT(*) >= 2
            ) as final_attendance
            union all
            select
                :source_permit as source
                , DATE(p.date) as date
                , case
                    when type = :type_sick then :color_sick
                    when type = :type_paid_leave then :color_paid_leave
                    when type = :type_leave then :color_leave
                    else NULL
                end as color1
                , null as color2
            from permits p
            where 1 = 1
                and p.user_id = :user_id2
                and
                (
                    DATE_FORMAT(p.date, '%Y-%m') = :selected_month_year3
                )
        ) as final
        order by final.date ASC
        ", [
            'source_attendance' => AttendancePermitSourceEnum::ATTENDANCE->value,
            'source_permit'     => AttendancePermitSourceEnum::PERMIT->value,
            'user_id1'  => $userAuth->id,
            'user_id2'  => $userAuth->id,
            'selected_month_year1' => $selectedMonthYear,
            'selected_month_year2' => $selectedMonthYear,
            'selected_month_year3' => $selectedMonthYear,
            'status1'   => AttendanceStatusEnum::REJECT->value,
            'status2'   => AttendanceStatusEnum::REJECT->value,
            'status3'   => AttendanceStatusEnum::REJECT->value,
            'status4'   => AttendanceStatusEnum::REJECT->value,
            'color_success1'    => $colorSuccess,
            'color_failed1'     => $colorLate,
            'color_success2'    => $colorSuccess,
            'color_failed2'     => $colorEarlyLeave,
            'color_sick'        => $colorSick,
            'color_paid_leave'  => $colorPaidLeave,
            'color_leave'       => $colorLeave,
            'type_sick'         => $permitTypeSick,
            'type_paid_leave'   => $permitTypePaidLeave,
            'type_leave'        => $permitTypeLeave,
        ]);

        (array) $data = AttendancePermitMonthResponse::collect(
            $result,
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function export(AttendanceExportRequest $req)
    {
        /** @var \Illuminate\Support\Collection */
        $users = User::whereIn('id', $req->usersId)->get(['id', 'full_name']);

        $filename =
            $req->startDate->format('Y-m-d')
            . '_'
            . $req->endDate->format('Y-m-d')
            . '_'
            . implode('_', $req->usersId)
            . '.xlsx';

        return (new AttendanceExport($users, $req->startDate, $req->endDate))
            ->download($filename);
    }

    // https://stackoverflow.com/questions/336127/calculate-business-days/19221403#19221403
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

    // https://stackoverflow.com/questions/8396507/get-number-of-weekdays-in-a-given-month
    private function get_weekdays(int $m, int $y)
    {
        $lastday = date("t", mktime(0, 0, 0, $m, 1, $y)); // Total day within selected month and year
        return $lastday;
        $weekdays = 0;
        for ($d = 29; $d <= $lastday; $d++) {
            $wd = date("w", mktime(0, 0, 0, $m, $d, $y));
            if ($wd > 0 && $wd < 6) {
                $weekdays++;
            }
        }
        return $weekdays + 20;
    }
}
