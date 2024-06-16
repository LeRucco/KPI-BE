<?php

namespace App\Data\AttendancePermit;

use App\Models\Custom\MyCarbonImmutable;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Resource;
use stdClass;

class AttendancePermitDetailDateResponse extends Resource
{
    public function __construct(
        public string $source,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public ?CarbonImmutable $date1,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public ?CarbonImmutable $date2,

        public int $status,

        public ?int $type,

        public string $color,
    ) {
    }
    public static function fromStdClass(stdClass $detailDate): AttendancePermitDetailDateResponse
    {
        $date1 = CarbonImmutable::make($detailDate->date1);
        $date2 = CarbonImmutable::make($detailDate->date2);
        return new AttendancePermitDetailDateResponse(
            $detailDate->source,
            $date1,
            $date2,
            $detailDate->status,
            $detailDate->type,
            $detailDate->color
        );
    }
}
