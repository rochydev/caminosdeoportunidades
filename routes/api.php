<?php

use App\Http\Controllers\Api\CandidateCvController;
use App\Http\Controllers\Api\CandidateProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\JobOfferController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class, 'updateimg']);


    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('/user', [ProfileController::class, 'user']);
    Route::get('/user/signin', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);

    Route::apiResource('candidate-profiles', CandidateProfileController::class);

    // CV del candidato autenticado
    Route::get('candidate-cv', [CandidateCvController::class, 'show']);
    Route::post('candidate-cv', [CandidateCvController::class, 'upsert']);
    Route::post('cv-experiences', [CandidateCvController::class, 'storeExperience']);
    Route::put('cv-experiences/{experience}', [CandidateCvController::class, 'updateExperience']);
    Route::delete('cv-experiences/{experience}', [CandidateCvController::class, 'destroyExperience']);
    Route::post('cv-educations', [CandidateCvController::class, 'storeEducation']);
    Route::put('cv-educations/{education}', [CandidateCvController::class, 'updateEducation']);
    Route::delete('cv-educations/{education}', [CandidateCvController::class, 'destroyEducation']);

    Route::apiResource('job-offers', JobOfferController::class);
    Route::apiResource('job-applications', JobApplicationController::class);

    Route::get('abilities', function (Request $request) {
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

// Rutas públicas de ofertas (solo PUBLISHED)
Route::get('public/job-offers', function (Request $request) {
    $query = \App\Models\JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality'])
        ->where('status', 'PUBLISHED');

    if ($request->filled('search')) {
        $s = $request->search;
        $query->where(fn($q) => $q->where('title', 'like', "%$s%")->orWhere('description', 'like', "%$s%"));
    }
    if ($request->filled('city'))               $query->where('city', 'like', '%'.$request->city.'%');
    if ($request->filled('category_id'))        $query->where('category_id', $request->category_id);
    if ($request->filled('contract_type_id'))   $query->where('contract_type_id', $request->contract_type_id);
    if ($request->filled('modality_id'))        $query->where('modality_id', $request->modality_id);
    if ($request->filled('workday_type_id'))    $query->where('workday_type_id', $request->workday_type_id);
    if ($request->filled('is_adapted'))         $query->where('is_adapted', (bool) $request->is_adapted);

    return $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 12));
});

Route::get('public/job-offers/{id}', function ($id) {
    $offer = \App\Models\JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality'])
        ->findOrFail($id);
    return response()->json(['data' => $offer]);
});

Route::get('category-list', [CategoryController::class, 'getList']);
Route::get('disability-types', fn() => response()->json(\App\Models\DisabilityType::orderBy('name')->get()));
Route::get('job-categories', fn() => response()->json(\App\Models\JobCategory::orderBy('name')->get()));
Route::get('contract-types', fn() => response()->json(\App\Models\ContractType::orderBy('name')->get()));
Route::get('workday-types', fn() => response()->json(\App\Models\WorkdayType::orderBy('name')->get()));
Route::get('modality-types', fn() => response()->json(\App\Models\ModalityType::orderBy('name')->get()));


