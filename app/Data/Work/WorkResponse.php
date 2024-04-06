<?php

namespace App\Data\Work;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Resource;
use App\Models\Custom\MyCarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class WorkResponse extends Resource
{
    public function __construct(
        public string $id,

        // TODO WorkRatio

        public string $name,

        public string $description,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public ?CarbonImmutable $deletedAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $createdAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $updatedAt,
    ) {
    }
}
