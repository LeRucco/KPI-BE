<?php

namespace App\Data\Paycheck;

use App\Data\PaycheckFile\PaycheckFileResponse;
use App\Data\User\UserResponse;
use App\Models\Custom\MyCarbon;
use App\Models\Paycheck;
use App\Models\User;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCastAndTransformer;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;
use stdClass;

#[MapName(SnakeCaseMapper::class)]
class PaycheckResponse extends Resource
{
    public function __construct(
        public string $id,

        public string $userId,

        public string $userFullName,

        public Lazy | UserResponse $user,

        public string $description,

        #[WithCastAndTransformer(MyCarbon::class)]
        public Carbon $date,

        #[DataCollectionOf(PaycheckFileResponse::class)]
        public DataCollection $files,
    ) {
    }

    public static function fromModel(Paycheck $paycheck): PaycheckResponse
    {
        $userData = Lazy::create(fn () => UserResponse::fromModel($paycheck->user));

        (string) $userFullName = $paycheck->user->full_name;

        /** @var DataCollection */
        $filesData = PaycheckFileResponse::collect(
            $paycheck->getMedia(Paycheck::FILE),
            DataCollection::class
        );

        return new PaycheckResponse(
            $paycheck->id,
            $paycheck->user_id,
            $userFullName,
            $userData,
            $paycheck->description,
            Carbon::make($paycheck->date),
            $filesData,
        );
    }

    public static function fromStdClass(stdClass $paycheck): PaycheckResponse
    {
        $userData = Lazy::create(fn () => UserResponse::fromModel(Paycheck::find($paycheck->user_id)));

        (string) $userFullName = User::find($paycheck->user_id)->full_name;

        /** @var DataCollection */
        $filesData = PaycheckFileResponse::collect(
            Paycheck::find($paycheck->id)->getMedia(Paycheck::FILE),
            DataCollection::class
        );

        return new PaycheckResponse(
            $paycheck->id,
            $paycheck->user_id,
            $userFullName,
            $userData,
            $paycheck->description,
            Carbon::make($paycheck->date),
            $filesData,
        );
    }
}
