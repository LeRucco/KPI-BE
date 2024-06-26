<?php

namespace App\Data\User;

use App\Models\User;
use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UserUpdateImageRequest extends Data
{
    public function __construct(

        // #[Exists(User::class, 'id')]
        // public string $userId,

        #[Max(1024 * 1024 * 10), Image()]
        public UploadedFile $image,

    ) {
    }
}
