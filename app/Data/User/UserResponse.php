<?php

namespace App\Data\User;

use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonImmutable;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Resource;
use App\Models\Custom\MyCarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class UserResponse extends Resource
{

    public function __construct(
        public string $id,

        public string $nrp,

        public ?UserImageResponse $image,

        public ?string $position,

        public string $fullName,

        public ?string $nik,

        public ?string $bpjsKetenagakerjaan,

        public ?string $bpjsKesehatan,

        public ?int $payrate,

        public ?string $npwp,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $doh,

        public ?string $birthPlace,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $birthDate,

        public ?string $religion,

        public ?string $phoneNumber,

        public ?string $email,

        public ?string $city,

        public ?string $address,

        public ?string $status,

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
        $imageData = UserImageResponse::fromCollection(
            $user->getMedia(User::IMAGE)
        );

        return new UserResponse(
            $user->id,
            $user->nrp,
            $imageData,
            $user->position,
            $user->full_name,
            $user->nik,
            $user->bpjs_ketenagakerjaan,
            $user->bpjs_kesehatan,
            $user->payrate,
            $user->npwp,
            Carbon::make($user->doh),
            $user->birth_place,
            Carbon::make($user->birth_date),
            $user->religion,
            $user->phone_number,
            $user->email,
            $user->city,
            $user->address,
            $user->status,
            CarbonImmutable::make($user->deleted_at),
            CarbonImmutable::make($user->created_at),
            CarbonImmutable::make($user->updated_at),
        );
    }
}
