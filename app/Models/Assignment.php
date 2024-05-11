<?php

namespace App\Models;

use App\Models\User;
use App\Models\Work;

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Assignment extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    const IMAGE = 'assignment_image';

    protected $table = 'assignments';

    protected $fillable = [
        'user_id',
        'work_id',
        'date',
        'description',
        'latitude',
        'longitude',
        'location_address'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class, 'work_id', 'id');
    }

    public function resolveRouteBinding($id, $field = null): \Illuminate\Database\Eloquent\Model|null
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::ASSIGNMENT_READTRASHED->value,
        ]))
            return $this::withTrashed()->find($id);

        return $this::find($id);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this::IMAGE)
            ->useDisk($this::IMAGE);
        // ->registerMediaConversions(function (Media $media) {
        //     $this
        //         ->addMediaConversion('thumb')
        //         ->width(50)
        //         ->height(50);
        // });
    }
}
