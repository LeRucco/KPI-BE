<?php

namespace App\Models;

use App\Models\Work;
use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkRatio extends Model
{
    use SoftDeletes;

    protected $table = 'work_ratio';

    protected $fillable = [
        'work_id',
        'percentage',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Work::class, 'work_id', 'id');
    }

    public function resolveRouteBinding($id, $field = null): \Illuminate\Database\Eloquent\Model|null
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::WORKRATIO_READTRASHED->value,
        ]))
            return $this::withTrashed()->find($id);

        return $this::find($id);
    }
}
