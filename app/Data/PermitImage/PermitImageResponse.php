<?php

namespace App\Data\PermitImage;

use Spatie\LaravelData\Resource;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[MapName(SnakeCaseMapper::class)]
class PermitImageResponse extends Resource
{
    public function __construct(

        public string $id,

        public string $uuid,

        public string $name,

        public string $url
    ) {
    }

    public static function fromMedia(Media $media): PermitImageResponse
    {

        return new PermitImageResponse(
            $media->id,
            $media->uuid,
            $media->name,
            $media->getFullUrl()
        );
    }
}
