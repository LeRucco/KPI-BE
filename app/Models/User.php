<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use App\Enums\PermissionEnum;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, InteractsWithMedia, SoftDeletes;

    const IMAGE = 'user_image';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'position',
        'nrp',
        'full_name',
        'nik',
        'bpjs_ketenagakerjaan',
        'bpjs_kesehatan',
        'payrate',
        'npwp',
        'doh',
        'birth_place',
        'birth_date',
        'religion',
        'phone_number',
        'email',
        'city',
        'address',
        'status',
        'image',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //     ];
    // }

    public function resolveRouteBinding($id, $field = null): \Illuminate\Database\Eloquent\Model|null
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if ($user->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::USER_READTRASHED->value,
        ]))
            return $this::withTrashed()->find($id);

        return $this::find($id);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection($this::IMAGE)
            ->useDisk($this::IMAGE)
            ->singleFile();
    }
}
