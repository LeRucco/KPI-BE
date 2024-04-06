<?php

namespace App\Data\Work;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Max;

#[MapName(SnakeCaseMapper::class)]
class WorkCreateRequest extends Data
{
    public function __construct(

        #[Max(50)]
        public string $name,

        #[Max(200)]
        public string $description,

    ) {
    }
}
