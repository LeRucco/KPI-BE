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
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[MapName(SnakeCaseMapper::class)]
class AssignmentResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $userId,

        public Lazy | UserResponse $user,

        public string $workId,

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

        /** @var DataCollection */
        $imagesData = AssignmentImageResponse::collect(
            $assignment->getMedia(Assignment::IMAGE),
            DataCollection::class
        );

        return new AssignmentResponse(
            $assignment->id,
            $assignment->user_id,
            $userData,
            $assignment->work_id,
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
