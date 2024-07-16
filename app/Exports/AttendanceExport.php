<?php

namespace App\Exports;

use App\Exports\Sheets\MonthlyAttendanceSheet;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AttendanceExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        /** @var Collection */
        private Collection $users,

        private CarbonImmutable $startDate,

        private CarbonImmutable $endDate
    ) {
    }

    public function sheets(): array
    {
        $startDate = $this->startDate->format('Y-m-d');
        $endDate = $this->endDate->format('Y-m-d');
        return $this->users->map(function ($user) use ($startDate, $endDate) {
            return new MonthlyAttendanceSheet(
                $user->id,
                $user->full_name,
                $startDate,
                $endDate
            );
        })->toArray();
    }
}
