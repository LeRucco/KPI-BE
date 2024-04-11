<?php

namespace App\Data\WorkRatio;

use App\Models\Custom\MyDecimal;
use App\Models\Work;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class WorkRatioCreateRequest extends Data
{
    public function __construct(

        #[Exists(Work::class, 'id')]
        public string $workId,

        #[WithCastAndTransformer(MyDecimal::class), Min(0.00), Max(100.00)]
        public float $percentage,
    ) {
    }
}
