<?php

namespace App\Data\Assignment;

use Carbon\Carbon;
use App\Models\Work;
use Spatie\LaravelData\Data;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AssignmentCreateRequest extends Data
{
    public function __construct(

        /// User Id can be taken from the token
        // #[Exists(User::class, 'id')]
        // public string $userId,

        #[Exists(Work::class, 'id')]
        public string $workId,

        #[Date(), WithCastAndTransformer(MyCarbon::class)]
        public Carbon $date,

        #[Max(200)]
        public string $description,

        #[Max(50)]
        public string $latitude,

        #[Max(50)]
        public string $longitude,

        #[Max(200)]
        public string $locationAddress,

        /** @var UploadedFile */
        #[Max(1024 * 1024 * 10)]
        public ?array $images,

    ) {
    }
}
