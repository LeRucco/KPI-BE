<?php

namespace App\Data\Work;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;


class WorkUpdateRequest extends Data
{

    public function __construct(
        #[Max(50)]
        public string $name,

        #[Max(200)]
        public string $description,
    ) {
    }
}
