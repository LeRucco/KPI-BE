<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DevController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(DevController::route . '/role-enum', [DevController::class, 'roleEnum']);
Route::get(DevController::route . '/permission-enum', [DevController::class, 'permissionEnum']);

Route::post(AuthenticationController::route . '/login', [AuthenticationController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    /** Authentication */
    Route::get(AuthenticationController::route . '/current-user', [AuthenticationController::class, 'currentUser']);
    Route::post(AuthenticationController::route . '/logout', [AuthenticationController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
