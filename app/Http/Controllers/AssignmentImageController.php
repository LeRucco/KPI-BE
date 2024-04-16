<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Models\AssignmentImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Data\AssignmentImage\AssignmentImageResponse;
use Spatie\LaravelData\PaginatedDataCollection;

class AssignmentImageController extends Controller implements ApiBasicReadInterfaces
{
    const route = 'assignment-image';

    public function index()
    {
        Gate::authorize('viewAny', [AssignmentImage::class]);

        (array) $data = AssignmentImageResponse::collect(
            $this->readTrashedOrNot()
                ->orderBy('id', 'desc')
                ->paginate(),
            PaginatedDataCollection::class
        )->toArray();

        return $this->successPaginate($data, Response::HTTP_OK, 'TODO');
    }

    public function show(AssignmentImage $assignmentImage)
    {
        Gate::authorize('view', [$assignmentImage]);

        (array) $data = AssignmentImageResponse::from(
            $assignmentImage
        )
            ->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function store()
    {
    }

    public function readTrashedOrNot(): \Illuminate\Database\Eloquent\Builder
    {
        /** @var \App\Models\User */
        $userAuth = Auth::user();

        if ($userAuth->canAny([
            PermissionEnum::KPI_READTRASHED->value,
            PermissionEnum::ASSIGNMENTIMAGE_READTRASHED->value
        ]))
            return AssignmentImage::query()->withTrashed();

        return AssignmentImage::query();
    }
}
