<?php

namespace App\Exports\Sheets;

use App\Enums\PermitStatusEnum;
use App\Enums\PermitTypeEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonthlyAttendanceSheet implements FromQuery, WithHeadings, ShouldAutoSize, WithTitle
{
    use Exportable;

    public function __construct(
        private string $userId,
        private string $fullName,
        private string $startDate,
        private string $endDate
    ) {
    }

    public function query()
    {
        // Subquery 1: Select from attendances
        $attendances = DB::table('attendances')
            ->selectRaw('
                IFNULL(clock_in, clock_out) as date
                , CASE 
                    WHEN clock_in IS NOT NULL THEN "In" 
                    WHEN clock_out IS NOT NULL THEN "Out"
                    ELSE "Unknown"
                END as type
                , CASE
                    WHEN status = ? THEN ?
                    WHEN status = ? THEN ?
                    WHEN status = ? THEN ?
                    ELSE "Unknown"
                END AS status
                , description
            ', [
                PermitStatusEnum::WAITING->value, PermitStatusEnum::WAITING->indonesia(),
                PermitStatusEnum::APPROVE->value, PermitStatusEnum::APPROVE->indonesia(),
                PermitStatusEnum::REJECT->value, PermitStatusEnum::REJECT->indonesia()
            ])
            ->where('user_id', $this->userId)
            ->where(function ($query) {
                $query->whereBetween(DB::raw('DATE(clock_in)'), [$this->startDate, $this->endDate])
                    ->orWhereBetween(DB::raw('DATE(clock_out)'), [$this->startDate, $this->endDate]);
            });

        // Subquery 2: Select from permits
        $permits = DB::table('permits')
            ->selectRaw('
                date 
                , CASE 
                    WHEN type = ? THEN ?
                    WHEN type = ? THEN ?
                    WHEN type = ? THEN ?
                    ELSE "Unknown"
                END AS type
                , CASE
                    WHEN status = ? THEN ?
                    WHEN status = ? THEN ?
                    WHEN status = ? THEN ?
                    ELSE "Unknown"
                END AS status
                , description
            ', [
                PermitTypeEnum::SICK->value, PermitTypeEnum::SICK->indonesia(),
                PermitTypeEnum::PAID_LEAVE->value, PermitTypeEnum::PAID_LEAVE->indonesia(),
                PermitTypeEnum::LEAVE->value, PermitTypeEnum::LEAVE->indonesia(),
                PermitStatusEnum::WAITING->value, PermitStatusEnum::WAITING->indonesia(),
                PermitStatusEnum::APPROVE->value, PermitStatusEnum::APPROVE->indonesia(),
                PermitStatusEnum::REJECT->value, PermitStatusEnum::REJECT->indonesia()
            ])
            ->where('user_id', $this->userId)
            ->whereBetween(DB::raw('DATE(date)'), [$this->startDate, $this->endDate]);

        // Combine both subqueries using unionAll
        $combine = $attendances->unionAll($permits);

        // Final query to order the results
        return DB::table(DB::raw("({$combine->toSql()}) as combine"))
            ->mergeBindings($combine)
            ->orderBy('combine.date', 'asc');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Status',
            'Description'
        ];
    }

    public function title(): string
    {
        return $this->fullName;
    }
}
