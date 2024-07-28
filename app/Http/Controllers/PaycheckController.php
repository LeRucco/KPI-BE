<?php

namespace App\Http\Controllers;

use App\Models\Paycheck;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Data\Paycheck\PaycheckResponse;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Data\Paycheck\PaycheckCreateRequest;
use App\Data\Paycheck\PaycheckReportRequest;
use App\Data\Paycheck\PaycheckYearlyRequest;
use App\Exceptions\ModelTrashedException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;

class PaycheckController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'paycheck';

    public function index()
    {
        Gate::authorize('viewAny', [Paycheck::class]);

        (array) $data = PaycheckResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )
            ->include('user')
            ->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(Paycheck $paycheck)
    {
        Gate::authorize('view', [Paycheck::class]);
        Gate::authorize('viewFiles', [Paycheck::class]);

        (array) $data = PaycheckResponse::from(
            $paycheck
        )
            ->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function yearly(PaycheckYearlyRequest $req)
    {
        Gate::authorize('yearly', [Paycheck::class]);

        $date = $req->date->format('Y');

        /** @var \App\Models\User */
        $userAuthId = Auth::user()->id;

        $result = DB::table('paychecks')
            ->where('user_id', '=', $userAuthId)
            ->where(DB::raw("DATE_FORMAT(date, '%Y')"), $date)
            ->orderBy('date', 'asc')
            ->get();

        (array) $data = PaycheckResponse::collect(
            $result,
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function report(PaycheckReportRequest $req)
    {
        Gate::authorize('report', [Paycheck::class]);

        $date = $req->date->format('Y-m');
        $userId = $req->userId;

        $result = DB::table('paychecks')
            ->when($userId, function (Builder $query) use ($userId) {
                $query->where('user_id', '=', $userId);
            })
            ->where(DB::raw("DATE_FORMAT(date, '%Y-%m')"), $date)
            ->orderBy('id', 'desc')
            ->get();

        (array) $data = PaycheckResponse::collect(
            $result,
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    // TODO throw exception untuk mencegah duplikasi
    public function store(PaycheckCreateRequest $req)
    {
        Gate::authorize('create', [Paycheck::class]);
        Gate::authorize('createFiles', [Paycheck::class]);

        /** @var \App\Models\Paycheck */
        $paycheck = Paycheck::create($req->except('files')->toArray());

        $monthYear = $req->date->format('Y-m');

        if ($req->files !== null)
            foreach ($req->files as $index => $uploadedFile) {
                $paycheck
                    ->addMedia($uploadedFile)
                    ->usingName($paycheck->id . '-' . $paycheck->user_id . '-' . $monthYear . '-' . $index)
                    ->toMediaCollection(Paycheck::FILE);
            }

        (array) $data = PaycheckResponse::from(
            $paycheck
        )
            ->include('user')
            ->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    // TODO Update function

    public function destroy(Paycheck $paycheck)
    {
        // TODO Policy

        if ($paycheck->trashed())
            throw ModelTrashedException::alreadySoftDeleted();

        (bool) $isSuccess = $paycheck->delete();
        (array) $data = PaycheckResponse::from(
            $paycheck
        )
            ->toArray();

        if ($isSuccess)
            return $this->success($data, Response::HTTP_OK, 'TODO');

        return $this->error($data, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function restore(Paycheck $paycheck)
    {
        // TODO Policy

        if (!$paycheck->trashed())
            throw ModelTrashedException::stillExist();

        (bool) $isSuccess = $paycheck->restore();

        if ($isSuccess) {
            (array) $data = PaycheckResponse::from(
                $paycheck
            )
                ->toArray();

            return $this->success($data, Response::HTTP_OK, 'TODO');
        }

        return $this->error(null, Response::HTTP_BAD_REQUEST, 'TODO');
    }

    public function readTrashedOrNot(): \Illuminate\Database\Eloquent\Builder
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        if ($userAuth->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::PAYCHECK_READTRASHED->value
        ]))
            return Paycheck::query()->withTrashed();

        return Paycheck::query();
    }
}
