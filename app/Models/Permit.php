<?php

namespace App\Models;

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permit extends Model
{
    use SoftDeletes;

    protected $table = 'permits';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'date',
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
}
