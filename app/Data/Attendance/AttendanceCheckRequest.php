<?php

namespace App\Data\Attendance;

use App\Models\User;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use App\Enums\AttendanceStatusEnum;
use Spatie\LaravelData\Attributes\MapName;
use App\Models\Custom\MyCarbonImmutableDate;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AttendanceCheckRequest extends Data
{
    public function __construct(

        #[Date(), WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $date,

        #[Enum(AttendanceStatusEnum::class)]
        public ?AttendanceStatusEnum $status,

        #[Exists(User::class, 'id')]
        public ?String $userId,
    ) {
    }
}
