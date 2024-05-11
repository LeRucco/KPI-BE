<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentImageController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\WorkRatioController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(DevController::route . '/ping', function () {
    return 'hello world';
});

Route::get(DevController::route . '/role-enum', [DevController::class, 'roleEnum']);
Route::get(DevController::route . '/permission-enum', [DevController::class, 'permissionEnum']);
Route::get(DevController::route . '/hesoyam', [DevController::class, 'hesoyam']);
Route::get(DevController::route . '/water-color', [DevController::class, 'watercolor']);

Route::post(AuthenticationController::route . '/login', [AuthenticationController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    /** Authentication */
    Route::get(AuthenticationController::route . '/current-user', [AuthenticationController::class, 'currentUser']);
    Route::post(AuthenticationController::route . '/logout', [AuthenticationController::class, 'logout']);

    /** User Profile */
    Route::apiResource(UserController::route, UserController::class);
    Route::post(UserController::route . '/{user}/image', [UserController::class, 'updateImage']);

    /** Attendance */
    Route::apiResource(AttendanceController::route, AttendanceController::class);
    Route::get(AttendanceController::route . '/{user}/user', [AttendanceController::class, 'user']);
    Route::post(AttendanceController::route . '/{attendance}/restore', [AttendanceController::class, 'restore']);
    Route::put(AttendanceController::route . '/{attendance}/update-status', [AttendanceController::class, 'updateStatus']);

    /** Work */
    Route::apiResource(WorkController::route, WorkController::class);
    Route::post(WorkController::route . '/{work}/restore', [WorkController::class, 'restore']);

    /** Work Ratio */
    Route::apiResource(WorkRatioController::route, WorkRatioController::class);
    Route::post(WorkRatioController::route . '/{work_ratio}/restore', [WorkRatioController::class, 'restore']);

    /** Assignment */
    Route::apiResource(AssignmentController::route, AssignmentController::class);
    Route::get(AssignmentController::route . '/{user}/user', [AssignmentController::class, 'user']);
    Route::post(AssignmentController::route . '/{assignment}/restore', [AssignmentController::class, 'restore']);

    /** Assignment Image */
    Route::get(AssignmentImageController::route . '/{assignment}', [AssignmentImageController::class, 'show']);
    Route::post(AssignmentImageController::route, [AssignmentImageController::class, 'store']);
    Route::delete(AssignmentImageController::route . '/{assignment}/{uuid}', [AssignmentImageController::class, 'destroy']);

    /** Role and Permission */
    Route::get(RolePermissionController::route . '/{user}/user', [RolePermissionController::class, 'user']);
});

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
