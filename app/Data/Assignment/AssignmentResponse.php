<?php

namespace App\Data\Assignment;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Lazy;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Resource;
use App\Data\User\UserResponse;
use App\Data\Work\WorkResponse;
use App\Models\Assignment;
use App\Models\Custom\MyCarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AssignmentResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $userId,

        public Lazy | UserResponse $user,

        public string $workId,

        public Lazy | WorkResponse $work,

        #[WithCastAndTransformer(MyCarbon::class)]
        public Carbon $date,

        public string $description,

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

    public static function fromModel(Assignment $assignment): AssignmentResponse
    {
        $userData = Lazy::create(fn () => UserResponse::from($assignment->user));

        $workData = Lazy::create(fn () => WorkResponse::from($assignment->work));

        return new AssignmentResponse(
            $assignment->id,
            $assignment->user_id,
            $userData,
            $assignment->work_id,
            $workData,
            Carbon::make($assignment->date),
            $assignment->description,
            $assignment->latitude,
            $assignment->longitude,
            $assignment->location_address,
            CarbonImmutable::make($assignment->deleted_at),
            CarbonImmutable::make($assignment->created_at),
            CarbonImmutable::make($assignment->updated_at),
        );
    }
}
