<?php

namespace App\Data\PaycheckFile;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[MapName(SnakeCaseMapper::class)]
class PaycheckFileResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $uuid,

        public string $name,

        public string $url
    ) {
    }

    public static function fromMedia(Media $media): PaycheckFileResponse
    {
        return new PaycheckFileResponse(
            $media->id,
            $media->uuid,
            $media->name,
            $media->getFullUrl()
        );
    }
}
