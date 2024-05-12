<?php

namespace App\Data\Permit;

use App\Enums\PermitStatusEnum;
use App\Enums\PermitTypeEnum;
use App\Models\Custom\MyCarbon;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PermitUpdateRequest extends Data
{
    public function __construct(

        #[Enum(PermitTypeEnum::class)]
        public PermitTypeEnum $type,

        #[Enum(PermitStatusEnum::class)]
        public PermitStatusEnum $status,

        #[Date(), WithCastAndTransformer(MyCarbon::class)]
        public Carbon $date,

        #[Max(200)]
        public ?string $description,
    ) {
    }
}
