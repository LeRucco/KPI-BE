<?php

namespace App\Data\RolePermission;


use Carbon\Carbon;
use App\Models\Attendance;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Lazy;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Resource;
use App\Data\User\UserResponse;
use App\Enums\AttendanceStatusEnum;
use App\Models\Custom\MyCarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class RolePermissionResponse extends Resource
{
    public function __construct(
        /** @var Collection<int, string> */
        public Collection $roles,

        /** @var Collection<int, string> */
        public Collection $permissions,
    ) {
    }

    public static function fromCollection(Collection $roles, Collection $permission): RolePermissionResponse
    {
        return new RolePermissionResponse(
            $roles,
            $permission->pluck('name')
        );
    }
}
