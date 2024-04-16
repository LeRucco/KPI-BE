<?php

namespace App\Data\AssignmentImage;

use Spatie\LaravelData\Resource;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class AssignmentImageResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $assignmentId,

        public string $image,
    ) {
    }
}
