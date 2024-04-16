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

class Assignment extends Model
{
    use SoftDeletes;

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

    public function images(): HasMany
    {
        return $this->hasMany(AssignmentImage::class, 'assignment_id', 'id');
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
}
