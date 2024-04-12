<?php

namespace App\Data\Attendance;


use Carbon\Carbon;
use App\Models\Attendance;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Lazy;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Resource;
use App\Data\User\UserResponse;
use App\Enums\AttendanceStatusEnum;
use App\Models\Custom\MyCarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AttendanceResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $userId,

        public Lazy | UserResponse $user,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $clockIn,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $clockOut,

        public ?string $description,

        #[Enum(AttendanceStatusEnum::class)]
        public AttendanceStatusEnum $status,

        public string $latitude,

        public string $longitude,

        public string $locationAddress,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public ?CarbonImmutable $deletedAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $createdAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $updatedAt,
    ) {
    }

    public static function fromModel(Attendance $attendance): AttendanceResponse
    {
        $userData = Lazy::create(fn () => UserResponse::from($attendance->user));
        $status = AttendanceStatusEnum::from($attendance->status);

        return new AttendanceResponse(
            $attendance->id,
            $attendance->user_id,
            $userData,
            Carbon::make($attendance->clock_in),
            Carbon::make($attendance->clock_out),
            $attendance->description,
            $status,
            $attendance->latitude,
            $attendance->longitude,
            $attendance->location_address,
            CarbonImmutable::make($attendance->deleted_at),
            CarbonImmutable::make($attendance->created_at),
            CarbonImmutable::make($attendance->updated_at),
        );
    }
}
