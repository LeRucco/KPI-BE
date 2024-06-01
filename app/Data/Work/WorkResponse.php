<?php

namespace App\Data\Work;

use App\Models\Work;
use App\Models\WorkRatio;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Resource;
use App\Models\Custom\MyCarbonImmutable;
use App\Data\WorkRatio\WorkRatioResponse;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class WorkResponse extends Resource
{
    public function __construct(
        public string $id,

        public Lazy | WorkRatioResponse $workRatio,

        public int $percentage,

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

    public static function fromModel(Work $work): WorkResponse
    {
        $workRatioData = Lazy::create(fn () => WorkRatioResponse::from($work->ratio));

        return new WorkResponse(
            $work->id,
            $workRatioData,
            $workRatioData->percentage,
            $work->name,
            $work->description,
            CarbonImmutable::make($work->deleted_at),
            CarbonImmutable::make($work->created_at),
            CarbonImmutable::make($work->updated_at),
        );
    }
}
