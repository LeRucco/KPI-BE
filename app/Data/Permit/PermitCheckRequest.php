<?php

namespace App\Data\Permit;

use App\Models\User;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use App\Enums\PermitStatusEnum;
use App\Enums\PermitTypeEnum;
use Spatie\LaravelData\Attributes\MapName;
use App\Models\Custom\MyCarbonImmutableDate;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class PermitCheckRequest extends Data
{
    public function __construct(

        #[Date(), WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $date,

        #[Enum(PermitTypeEnum::class)]
        public ?PermitTypeEnum $type,

        #[Enum(PermitStatusEnum::class)]
        public ?PermitStatusEnum $status,

        #[Exists(User::class, 'id')]
        public ?String $userId,
    ) {
    }
}
