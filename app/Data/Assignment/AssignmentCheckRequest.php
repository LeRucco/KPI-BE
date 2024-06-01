<?php

namespace App\Data\Assignment;

use App\Models\User;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapName;
use App\Models\Custom\MyCarbonImmutableDate;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AssignmentCheckRequest extends Data
{
    public function __construct(

        #[Date(), WithCastAndTransformer(MyCarbonImmutableDate::class)]
        public CarbonImmutable $date,

        #[Exists(User::class, 'id')]
        public ?String $userId,
    ) {
    }
}
