<?php

namespace App\Models;

use App\Models\User;
use App\Models\Work;

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Paycheck extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    const FILE = 'paycheck_file';

    protected $table = 'paychecks';

    protected $fillable = [
        'user_id',
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
            PermissionEnum::ASSIGNMENT_READTRASHED->value,
        ]))
            return $this::withTrashed()->find($id);

        return $this::find($id);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this::FILE)
            ->useDisk($this::FILE);
        // ->registerMediaConversions(function (Media $media) {
        //     $this
        //         ->addMediaConversion('thumb')
        //         ->width(50)
        //         ->height(50);
        // });
    }
}
