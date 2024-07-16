<?php

namespace App\Data\Attendance;

use App\Models\User;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use App\Models\Custom\MyCarbonImmutableDate;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AttendanceExportRequest extends Data
{
    public function __construct(
        /** @var string */
        #[ArrayType()]
        public array $usersId,

        #[Date(), WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $startDate,

        #[Date(), WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $endDate,
    ) {
    }
}
