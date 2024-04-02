<?php

namespace App\Data\User;

use Spatie\LaravelData\Resource;
use Spatie\LaravelData\Attributes\MapName;
use App\Models\Custom\MyCarbonImmutable;
use App\Models\User;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UserResponse extends Resource
{

    public function __construct(
        public string $id,

        public string $nrp,

        public ?string $fullName,

        public ?string $address,

        public ?string $phoneNumber,

        public ?string $image,

        public ?string $npwp,

        public ?string $bpjs,

        public ?string $nik,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public ?CarbonImmutable $deletedAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $createdAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $updatedAt,
    ) {
    }

    public static function fromModel(User $user): UserResponse
    {

        return new UserResponse(
            $user->id,
            $user->nrp,
            $user->full_name,
            $user->address,
            $user->phone_number,
            $user->image,
            $user->npwp,
            $user->bpjs,
            $user->nik,
            CarbonImmutable::make($user->deleted_at),
            CarbonImmutable::make($user->created_at),
            CarbonImmutable::make($user->updated_at),
        );
    }
}
