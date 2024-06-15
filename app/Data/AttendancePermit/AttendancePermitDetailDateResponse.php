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
        public CarbonImmutable $date,

        public int $status,

        public ?int $type,

        public string $color,
    ) {
    }
    public static function fromStdClass(stdClass $detailDate): AttendancePermitDetailDateResponse
    {
        $date = CarbonImmutable::make($detailDate->date);
        return new AttendancePermitDetailDateResponse(
            $detailDate->source,
            $date,
            $detailDate->status,
            $detailDate->type,
            $detailDate->color
        );
    }
}
