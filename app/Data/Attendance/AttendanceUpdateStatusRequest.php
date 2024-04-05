<?php

namespace App\Data\Attendance;

use App\Enums\AttendanceStatusEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class AttendanceUpdateStatusRequest extends Data
{
    public function __construct(

        #[Enum(AttendanceStatusEnum::class)]
        public AttendanceStatusEnum $status,

    ) {
    }
}
