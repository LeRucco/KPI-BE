<?php

namespace App\Data\Paycheck;

use Carbon\Carbon;
use App\Models\User;
use Spatie\LaravelData\Data;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class PaycheckReportRequest extends Data
{

    public function __construct(
        #[Exists(User::class, 'id')]
        public ?string $userId,

        #[Date(), WithCastAndTransformer(MyCarbon::class)]
        public Carbon $date,
    ) {
    }
}
