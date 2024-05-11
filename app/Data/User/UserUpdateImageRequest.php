<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UserUpdateImageRequest extends Data
{
    public function __construct(

        public int $userId,

        #[Max(1024 * 1024 * 10)]
        public UploadedFile $image,

    ) {
    }
}
