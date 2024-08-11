<?php

namespace App\Data\User;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use App\Models\Custom\MyCarbon;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;

#[MapName(SnakeCaseMapper::class)]
class UserUpdateRequest extends Data
{

    public function __construct(

        #[Max(50)]
        public string $fullName,

        #[Max(16)]
        public string $nik,

        #[Max(13)]
        public string $bpjsKetenagakerjaan,

        #[Max(13)]
        public string $bpjsKesehatan,

        public ?int $payrate,

        #[Max(16)]
        public string $npwp,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $doh,

        #[Max(20)]
        public ?string $birthPlace,

        #[WithCastAndTransformer(MyCarbon::class)]
        public ?Carbon $birthDate,

        #[Max(10)]
        public ?string $religion,

        #[Max(20)]
        public string $phoneNumber,

        #[Max(100)]
        public ?string $email,

        #[Max(20)]
        public ?string $city,

        #[Max(200)]
        public string $address,

        #[Max(20)]
        public ?string $status,

    ) {}
}
