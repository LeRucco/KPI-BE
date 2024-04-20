<?php

namespace App\Http\Controllers;

use App\Data\AssignmentImage\AssignmentImageCreateRequest;
use App\Data\AssignmentImage\AssignmentImageDeleteRequest;
use App\Enums\PermissionEnum;
use Illuminate\Http\Response;
use App\Models\AssignmentImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Interfaces\ApiBasicReadInterfaces;
use App\Data\AssignmentImage\AssignmentImageResponse;
use App\Exceptions\MediaModelException;
use App\Models\Assignment;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\DataCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AssignmentImageController extends Controller
{
    const route = 'assignment-image';

    public function show(Assignment $assignment)
    {
        Gate::authorize('viewImages', [$assignment]);

        (array) $data = AssignmentImageResponse::collect(
            $assignment->getMedia(Assignment::IMAGE),
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_OK, 'TODO');
    }

    public function store(AssignmentImageCreateRequest $req)
    {
        Gate::authorize('createImages', [Assignment::class]);

        /** @var \App\Models\Assignment */
        $assignment = Assignment::findOrFail($req->assignmentId)->first();

        if ($req->images !== null)
            foreach ($req->images as $index => $uploadedFile) {
                $assignment
                    ->addMedia($uploadedFile)
                    ->usingName($assignment->id . '-' . $assignment->user_id . '-' . $assignment->work_id . '-' . $index)
                    ->toMediaCollection(Assignment::IMAGE);
            }

        (array) $data = AssignmentImageResponse::collect(
            $assignment->getMedia(Assignment::IMAGE),
            DataCollection::class
        )->toArray();

        return $this->success($data, Response::HTTP_CREATED, 'TODO');
    }

    public function destroy(Assignment $assignment, string $uuid)
    {
        // TODO policy

        /** @var \Illumintate/Suppoert/Collection<int, Media> */
        $assignmentMedia = $assignment->getMedia(Assignment::IMAGE);

        (bool) $isMediaExists = $assignmentMedia->contains('uuid', $uuid);

        if (!$isMediaExists)
            throw MediaModelException::uuidNotFound();

        /** @var Media */
        $media = Media::findByUuid($uuid);

        $isSuccess = $media->delete();

        if ($isSuccess)
            return $this->success([], Response::HTTP_OK, 'TODO');

        return $this->error([], Response::HTTP_BAD_REQUEST, 'TODO');
    }

    // public function destroy(AssignmentImage $assignmentImage)
    // {
    //     return $assignmentImage->clearMediaCollection(AssignmentImage::ASSIGNMENT);
    // }

}
