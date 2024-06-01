<?php

namespace App\Data\User;

use App\Models\User;
use Spatie\LaravelData\Resource;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class UserDropdownResponse extends Resource
{

    public function __construct(
        public string $id,

        public string $nrp,

        public string $fullName,

    ) {
    }

    public static function fromModel(User $user): UserDropdownResponse
    {
        return new UserDropdownResponse(
            $user->id,
            $user->nrp,
            $user->full_name,
        );
    }
}
