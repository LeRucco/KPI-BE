<?php

namespace App\Data\User;

use Spatie\LaravelData\Resource;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[MapName(SnakeCaseMapper::class)]
class UserImageResponse extends Resource
{
    public function __construct(
        // public string $id,

        // public string $assignmentId,

        // public string $name,

        public string $id,

        public string $uuid,

        public string $name,

        public string $url
    ) {
    }

    // public static function fromModel(AssignmentImage $assignmentImage): AssignmentImageResponse
    // {
    //     $images = $assignmentImage->getMedia(AssignmentImage::ASSIGNMENT)->map(function (Media $media) {
    //         // return $media->getFullUrl() . '  ' . $media->getPath() . ' ' . $media->getUrl() . ' ' . $media->getPathRelativeToRoot();
    //         return $media->getFullUrl();
    //     });

    //     return new AssignmentImageResponse(
    //         $assignmentImage->id,
    //         $assignmentImage->assignment_id,
    //         $images,
    //     );
    // }

    public static function fromCollection(Collection $media): UserImageResponse | null
    {
        if ($media->count() === 0) return null;

        return new UserImageResponse(
            $media->first()->id,
            $media->first()->uuid,
            $media->first()->name,
            $media->first()->getFullUrl()
        );
    }

    // public static function fromMedia(Media $media): UserImageResponse | null
    // {
    //     if ($media === null) return null;

    //     return new UserImageResponse(
    //         $media->id,
    //         $media->uuid,
    //         $media->name,
    //         $media->getFullUrl()
    //     );
    // }
}
