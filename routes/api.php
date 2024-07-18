<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentImageController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendancePermitController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\WorkRatioController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(DevController::route . '/ping', function () {
    return 'hello world';
});

Route::get(DevController::route . '/attendance-export', [DevController::class, 'attendanceExport']);
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
    Route::post(UserController::route . '/profile/update-image', [UserController::class, 'updateImage']);
    Route::get(UserController::route . '/attendance/dropdown', [UserController::class, 'attendanceDropdown']);

    /** Attendance */
    Route::apiResource(AttendanceController::route, AttendanceController::class);
    Route::get(AttendanceController::route . '/{user}/user', [AttendanceController::class, 'user']);
    Route::post(AttendanceController::route . '/{attendance}/restore', [AttendanceController::class, 'restore']);
    Route::put(AttendanceController::route . '/{attendance}/update-status', [AttendanceController::class, 'updateStatus']);

    Route::get(AttendanceController::route . '/admin/check', [AttendanceController::class, 'check']);
    Route::get(AttendanceController::route . '/admin/total', [AttendanceController::class, 'totalAdminAttendance']);
    Route::get(AttendanceController::route . '/emp/today', [AttendanceController::class, 'today']);

    /** Attendance Permit */
    Route::get(AttendancePermitController::route . '/emp/total', [AttendancePermitController::class, 'totalEmp']);
    Route::get(AttendancePermitController::route . '/emp/month', [AttendancePermitController::class, 'month']);
    Route::get(AttendancePermitController::route . '/emp/detail-date', [AttendancePermitController::class, 'detailDate']);
    Route::get(AttendancePermitController::route . '/admin/export', [AttendancePermitController::class, 'export']);

    /** Work */
    Route::apiResource(WorkController::route, WorkController::class);
    Route::post(WorkController::route . '/{work}/restore', [WorkController::class, 'restore']);
    Route::get(WorkController::route . '/emp/daily', [WorkController::class, 'daily']);

    /** Work Ratio */
    Route::apiResource(WorkRatioController::route, WorkRatioController::class);
    Route::post(WorkRatioController::route . '/{work_ratio}/restore', [WorkRatioController::class, 'restore']);

    /** Assignment */
    Route::apiResource(AssignmentController::route, AssignmentController::class);
    Route::get(AssignmentController::route . '/{user}/user', [AssignmentController::class, 'user']);
    Route::post(AssignmentController::route . '/{assignment}/restore', [AssignmentController::class, 'restore']);
    Route::get(AssignmentController::route . '/admin/check', [AssignmentController::class, 'check']);
    Route::get(AssignmentController::route . '/emp/month', [AssignmentController::class, 'month']);
    Route::get(AssignmentController::route . '/emp/today', [AssignmentController::class, 'today']);

    /** Assignment Image */
    Route::get(AssignmentImageController::route . '/{assignment}', [AssignmentImageController::class, 'show']);
    Route::post(AssignmentImageController::route, [AssignmentImageController::class, 'store']);
    Route::delete(AssignmentImageController::route . '/{assignment}/{uuid}', [AssignmentImageController::class, 'destroy']);

    /** Role and Permission */
    Route::get(RolePermissionController::route . '/{user}/user', [RolePermissionController::class, 'user']);

    /** Permit */
    Route::apiResource(PermitController::route, PermitController::class);
    Route::get(PermitController::route . '/{user}/user', [PermitController::class, 'user']);
    Route::post(PermitController::route . '/{permit}/restore', [PermitController::class, 'restore']);
    Route::get(PermitController::route . '/emp/today', [PermitController::class, 'today']);
    Route::get(PermitController::route . '/admin/total', [PermitController::class, 'totalAdminPermit']);
    Route::get(PermitController::route . '/admin/check', [PermitController::class, 'check']);
    Route::put(PermitController::route . '/{permit}/update-status', [PermitController::class, 'updateStatus']);

    /** Paycheck */
});

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
