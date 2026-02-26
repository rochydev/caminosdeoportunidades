<?php

use App\Http\Controllers\Api\CandidateProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\JobOfferController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthenticatedSessionController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function() {

    Route::post('logout', [AuthenticatedSessionController::class, 'logout']);

    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class,'updateimg']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('/user', [ProfileController::class, 'user']);
    Route::get('/user/signin', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);


    /* Rutas nuevas - Se tienen que modificar y ponerle más restricciones */
    Route::apiResource('candidate-profiles', CandidateProfileController::class);
    Route::apiResource('job-applications', JobApplicationController::class);
    Route::apiResource('job-offers', JobOfferController::class);

    Route::get('abilities', function(Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });
});

Route::get('category-list', [CategoryController::class, 'getList']);

