<?php

namespace App\Data\AssignmentImage;

use Carbon\Carbon;
use App\Models\Work;
use Spatie\LaravelData\Data;
use App\Models\Custom\MyCarbon;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class AssignmentImageCreateRequest extends Data
{
    public function __construct(

        public int $assignmentId,

        #
        public UploadedFile $image,

    ) {
    }
}
