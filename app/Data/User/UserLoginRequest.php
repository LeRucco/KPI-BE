<?php

namespace App\Data\User;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UserLoginRequest extends Data
{
    public function __construct(
        #[Max(20)]
        public string $nrp,

        #[Max(100)]
        public string $password,
    ) {
    }
}
