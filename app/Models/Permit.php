<?php

namespace App\Models;

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Permit extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    const IMAGE = 'permit_image';

    protected $table = 'permits';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'date',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function resolveRouteBinding($id, $field = null): \Illuminate\Database\Eloquent\Model|null
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::PERMIT_READTRASHED->value,
        ]))
            return $this::withTrashed()->find($id);

        return $this::find($id);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this::IMAGE)
            ->useDisk($this::IMAGE);
    }
}
