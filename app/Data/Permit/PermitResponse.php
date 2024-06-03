<?php

namespace App\Data\Permit;

use App\Data\PermitImage\PermitImageResponse;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Lazy;
use App\Enums\PermitTypeEnum;
use App\Data\User\UserResponse;
use App\Enums\PermitStatusEnum;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Resource;
use App\Models\Custom\MyCarbonImmutable;
use App\Models\Permit;
use App\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use stdClass;

#[MapName(SnakeCaseMapper::class)]
class PermitResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $userId,

        public Lazy | UserResponse $user,

        #[Enum(PermitTypeEnum::class)]
        public PermitTypeEnum $type,

        public string $typeName,

        #[Enum(PermitStatusEnum::class)]
        public PermitStatusEnum $status,

        public string $statusName,

        #[DataCollectionOf(PermitImageResponse::class)]
        public DataCollection $images,

        #[WithCastAndTransformer(MyCarbon::class)]
        public Carbon $date,

        public string $description,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public ?CarbonImmutable $deletedAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $createdAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $updatedAt,
    ) {
    }

    public static function fromModel(Permit $permit): PermitResponse
    {

        $userData = Lazy::create(fn () => UserResponse::from($permit->user));
        $type = PermitTypeEnum::from($permit->type);
        $status = PermitStatusEnum::from($permit->status);

        /** @var DataCollection */
        $imagesData = PermitImageResponse::collect(
            $permit->getMedia(Permit::IMAGE),
            DataCollection::class
        );

        return new PermitResponse(
            $permit->id,
            $permit->user_id,
            $userData,
            $type,
            $type->name,
            $status,
            $status->name,
            $imagesData,
            Carbon::make($permit->date),
            $permit->description,
            CarbonImmutable::make($permit->deleted_at),
            CarbonImmutable::make($permit->created_at),
            CarbonImmutable::make($permit->updated_at),
        );
    }

    public static function fromStdClass(stdClass $permit): PermitResponse
    {
        $userData = Lazy::create(fn () => UserResponse::from(User::find($permit->user_id)));
        $type = PermitTypeEnum::from($permit->type);
        $status = PermitStatusEnum::from($permit->status);

        /** @var DataCollection */
        $imagesData = PermitImageResponse::collect(
            Permit::find($permit->id)->getMedia(Permit::IMAGE),
            DataCollection::class
        );

        return new PermitResponse(
            $permit->id,
            $permit->user_id,
            $userData,
            $type,
            $type->name,
            $status,
            $status->name,
            $imagesData,
            Carbon::make($permit->date),
            $permit->description,
            CarbonImmutable::make($permit->deleted_at),
            CarbonImmutable::make($permit->created_at),
            CarbonImmutable::make($permit->updated_at),
        );
    }
}
