<?php

namespace App\Data\AttendancePermit;

use App\Enums\AttendancePermitSourceEnum;
use App\Models\Custom\MyCarbonImmutableDate;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AttendancePermitDetailDateRequest extends Data
{
    public function __construct(

        #[Enum(AttendancePermitSourceEnum::class)]
        public AttendancePermitSourceEnum $source,

        #[Date(), WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $date,
    ) {
    }
}
