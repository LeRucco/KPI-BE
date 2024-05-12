<?php

namespace App\Data\Permit;

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
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

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

        #[WithCastAndTransformer(MyCarbon::class)]
        public Carbon $date,

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

        return new PermitResponse(
            $permit->id,
            $permit->user_id,
            $userData,
            $type,
            $type->name,
            $status,
            $status->name,
            Carbon::make($permit->date),
            CarbonImmutable::make($permit->deleted_at),
            CarbonImmutable::make($permit->created_at),
            CarbonImmutable::make($permit->updated_at),
        );
    }
}
