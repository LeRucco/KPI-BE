<?php

namespace App\Data\AttendancePermit;

use App\Models\Custom\MyCarbonImmutableDate;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Resource;


class AttendancePermitMonthResponse extends Resource
{
    public function __construct(
        public string $source,

        #[WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $date,

        public string $color1,

        public ?string $color2,
    ) {
    }

    public static function fromArray(array $month): AttendancePermitMonthResponse
    {
        return new AttendancePermitMonthResponse(
            $month['source'],
            $month['date'],
            $month['color1'],
            $month['color2'],
        );
    }

    // public static function fromStdClass(stdClass $month): AttendanceMonthResponse
    // {
    //     return new AttendanceMonthResponse(
    //         $month->source,
    //         $month->date,
    //         $month->color1,
    //         $month->color2,
    //     );
    // }
}
