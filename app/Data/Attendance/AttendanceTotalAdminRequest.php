<?php

namespace App\Data\Attendance;

use App\Models\Custom\MyCarbonImmutableDate;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class AttendanceTotalAdminRequest extends Data
{
    public function __construct(
        #[Date(), WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $date,
    ) {
    }
}
