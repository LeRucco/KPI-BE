<?php

namespace App\Data\WorkRatio;

use App\Data\Job\JobResponse;
use App\Data\Work\WorkResponse;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Resource;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use App\Models\Custom\MyCarbonImmutable;
use App\Models\Custom\MyDecimal;
use App\Models\JobRatio;
use App\Models\Job;
use App\Models\Work;
use App\Models\WorkRatio;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Lazy;

#[MapName(SnakeCaseMapper::class)]
class WorkRatioResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $workId,

        public Lazy | Work $work,

        #[WithCastAndTransformer(MyDecimal::class)]
        public float $percentage,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public ?CarbonImmutable $deletedAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $createdAt,

        #[WithCastAndTransformer(MyCarbonImmutable::class)]
        public CarbonImmutable $updatedAt,
    ) {
    }

    public static function fromModel(WorkRatio $workRatio): WorkRatioResponse
    {
        $workData = Lazy::create(fn () => WorkResponse::from($workRatio->job));

        return new WorkRatioResponse(
            $workRatio->id,
            $workRatio->work_id,
            $workData,
            $workRatio->percentage,
            CarbonImmutable::make($workRatio->deleted_at),
            CarbonImmutable::make($workRatio->created_at),
            CarbonImmutable::make($workRatio->updated_at),
        );
    }
}
