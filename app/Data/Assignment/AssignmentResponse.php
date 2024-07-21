<?php

namespace App\Data\Assignment;

use Carbon\Carbon;
use App\Models\Assignment;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Lazy;
use App\Data\User\UserResponse;
use App\Data\Work\WorkResponse;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Resource;
use Spatie\LaravelData\DataCollection;
use App\Models\Custom\MyCarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use App\Data\AssignmentImage\AssignmentImageResponse;
use App\Models\User;
use App\Models\Work;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use stdClass;

#[MapName(SnakeCaseMapper::class)]
class AssignmentResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $userId,

        public string $userFullName,

        public Lazy | UserResponse $user,

        public string $workId,

        public string $workName,

        public Lazy | WorkResponse $work,

        #[DataCollectionOf(AssignmentImageResponse::class)]
        public DataCollection $images,

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
        // TODO Include When Lazy Condition
        // https://spatie.be/docs/laravel-data/v4/as-a-resource/lazy-properties#content-types-of-lazy-properties
        $userData = Lazy::create(fn () => UserResponse::from($assignment->user));

        $workData = Lazy::create(fn () => WorkResponse::from($assignment->work));

        (string) $userFullName = $assignment->user->full_name;

        (string) $workName = $assignment->work->name;

        /** @var DataCollection */
        $imagesData = AssignmentImageResponse::collect(
            $assignment->getMedia(Assignment::IMAGE),
            DataCollection::class
        );

        return new AssignmentResponse(
            $assignment->id,
            $assignment->user_id,
            $userFullName,
            $userData,
            $assignment->work_id,
            $workName,
            $workData,
            $imagesData,
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

    public static function fromStdClass(stdClass $assignment): AssignmentResponse
    {

        $userData = Lazy::create(fn () => UserResponse::from(User::find($assignment->user_id)));

        $workData = Lazy::create(fn () => WorkResponse::from(Work::find($assignment->work_id)));

        (string) $userFullName = $assignment->user->full_name;

        (string) $workName = Work::find($assignment->work_id)->name;

        /** @var DataCollection */
        $imagesData = AssignmentImageResponse::collect(
            Assignment::find($assignment->id)->getMedia(Assignment::IMAGE),
            DataCollection::class
        );

        return new AssignmentResponse(
            $assignment->id,
            $assignment->user_id,
            $userFullName,
            $userData,
            $assignment->work_id,
            $workName,
            $workData,
            $imagesData,
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
