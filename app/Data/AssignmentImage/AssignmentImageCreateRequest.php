<?php

namespace App\Data\AssignmentImage;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class AssignmentImageCreateRequest extends Data
{
    public function __construct(

        public int $assignmentId,

        /** @var UploadedFile */
        #[Max(1024 * 1024 * 10)]
        public array $images,

    ) {
    }
}
