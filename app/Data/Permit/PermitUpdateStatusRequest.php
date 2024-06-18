<?php

namespace App\Data\Permit;

use App\Enums\PermitStatusEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PermitUpdateStatusRequest extends Data
{
    public function __construct(

        #[Enum(PermitStatusEnum::class)]
        public PermitStatusEnum $status,

    ) {
    }
}
