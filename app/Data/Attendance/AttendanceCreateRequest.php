<?php

namespace App\Data\Attendance;

use App\Enums\AttendanceStatusEnum;
use App\Models\Custom\MyCarbon;
use Carbon\Carbon;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AttendanceCreateRequest extends Data
{
    public function __construct(

        /// User Id can be taken from the token
        // #[Exists(User::class, 'id')]
        // public string $userId,

        #[Date(), WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $clockIn,

        #[Date(), WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $clockOut,

        #[Max(200)]
        public ?string $description,

        #[Enum(AttendanceStatusEnum::class)]
        public AttendanceStatusEnum $status,

        #[Max(50)]
        public string $latitude,

        #[Max(50)]
        public string $longitude,

        #[Max(200)]
        public string $locationAddress,

    ) {
    }
}
