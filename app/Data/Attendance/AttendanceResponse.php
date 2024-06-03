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
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use stdClass;

#[MapName(SnakeCaseMapper::class)]
class AttendanceResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $userId,

        public string $fullName,

        public Lazy | UserResponse $user,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $clockIn,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $clockOut,

        public ?string $description,

        #[Enum(AttendanceStatusEnum::class)]
        public AttendanceStatusEnum $status,

        public string $statusName,

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
        Log::debug('FROM MODEL LER');
        $userData = Lazy::create(fn () => UserResponse::from($attendance->user));
        $status = AttendanceStatusEnum::from($attendance->status);
        $userFullName = $attendance->user->full_name;

        return new AttendanceResponse(
            $attendance->id,
            $attendance->user_id,
            $userFullName,
            $userData,
            Carbon::make($attendance->clock_in),
            Carbon::make($attendance->clock_out),
            $attendance->description,
            $status,
            $status->name,
            $attendance->latitude,
            $attendance->longitude,
            $attendance->location_address,
            CarbonImmutable::make($attendance->deleted_at),
            CarbonImmutable::make($attendance->created_at),
            CarbonImmutable::make($attendance->updated_at),
        );
    }

    public static function fromArray(array $attendance): AttendanceResponse
    {
        $status = AttendanceStatusEnum::from($attendance['status']);
        $userData = Lazy::create(fn () => UserResponse::from(User::find($attendance['user_id'])));
        return new AttendanceResponse(
            $attendance['id'],
            $attendance['user_id'],
            $$attendance['full_name'],
            $userData,
            Carbon::make($attendance['clock_in']),
            Carbon::make($attendance['clock_out']),
            $attendance['description'],
            $status,
            $status->name,
            $attendance['latitude'],
            $attendance['longitude'],
            $attendance['location_address'],
            CarbonImmutable::make($attendance['deleted_at']),
            CarbonImmutable::make($attendance['created_at']),
            CarbonImmutable::make($attendance['updated_at']),
        );
    }

    public static function fromStdClass(stdClass $attendance): AttendanceResponse
    {
        $status = AttendanceStatusEnum::from($attendance->status);
        $userData = Lazy::create(fn () => UserResponse::from(User::find($attendance->user_id)));
        return new AttendanceResponse(
            $attendance->id,
            $attendance->user_id,
            $attendance->full_name,
            $userData,
            Carbon::make($attendance->clock_in),
            Carbon::make($attendance->clock_out),
            $attendance->description,
            $status,
            $status->name,
            $attendance->latitude,
            $attendance->longitude,
            $attendance->location_address,
            CarbonImmutable::make($attendance->deleted_at),
            CarbonImmutable::make($attendance->created_at),
            CarbonImmutable::make($attendance->updated_at),
        );
    }
}
