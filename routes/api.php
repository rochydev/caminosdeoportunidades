<?php

use App\Http\Controllers\Api\CandidateCvController;
use App\Http\Controllers\Api\CandidateProfileController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\JobOfferController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => 'auth:sanctum'], function () {

    /*
    |--------------------------------------------------------------------------
    | Perfil propio (cualquier usuario autenticado)
    |--------------------------------------------------------------------------
    */
    Route::get('/user', [ProfileController::class, 'user']);
    Route::get('/user/signin', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);

    // Editar el propio usuario o su avatar. La comprobación de propiedad
    // (solo uno mismo o un admin con 'user-edit') se hace en el controlador.
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::post('users/updateimg', [UserController::class, 'updateimg']);

    // Permisos efectivos del usuario autenticado (lo consume CASL en el frontend).
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

    /*
    |--------------------------------------------------------------------------
    | Administración — protegida por PERMISO (Spatie) por cada acción
    |--------------------------------------------------------------------------
    */

    // Usuarios
    Route::get('users', [UserController::class, 'index'])->middleware('permission:user-list');
    Route::post('users', [UserController::class, 'store'])->middleware('permission:user-create');
    Route::get('users/{user}', [UserController::class, 'show'])->middleware('permission:user-list');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('permission:user-delete');

    // Categorías (gestión)
    Route::get('categories', [CategoryController::class, 'index'])->middleware('permission:category-list');
    Route::post('categories', [CategoryController::class, 'store'])->middleware('permission:category-create');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->middleware('permission:category-list');
    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update'])->middleware('permission:category-edit');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:category-delete');

    // Roles
    Route::get('role-list', [RoleController::class, 'getList'])->middleware('permission:role-list');
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions'])->middleware('permission:role-list');
    Route::put('role-permissions', [PermissionController::class, 'updateRolePermissions'])->middleware('permission:role-edit');
    Route::get('roles', [RoleController::class, 'index'])->middleware('permission:role-list');
    Route::post('roles', [RoleController::class, 'store'])->middleware('permission:role-create');
    Route::get('roles/{role}', [RoleController::class, 'show'])->middleware('permission:role-list');
    Route::match(['put', 'patch'], 'roles/{role}', [RoleController::class, 'update'])->middleware('permission:role-edit');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:role-delete');

    // Permisos
    Route::get('permissions', [PermissionController::class, 'index'])->middleware('permission:permission-list');
    Route::post('permissions', [PermissionController::class, 'store'])->middleware('permission:permission-create');
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])->middleware('permission:permission-list');
    Route::match(['put', 'patch'], 'permissions/{permission}', [PermissionController::class, 'update'])->middleware('permission:permission-edit');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permission-delete');

    /*
    |--------------------------------------------------------------------------
    | Ofertas de empleo
    |--------------------------------------------------------------------------
    */
    // Recomendadas para el candidato (debe ir ANTES de job-offers/{jobOffer}).
    Route::get('job-offers/recommended', [JobOfferController::class, 'recommended'])->middleware('role:candidate|admin');

    // Lectura: cualquier usuario autenticado (un candidato navega ofertas estando logueado).
    Route::get('job-offers', [JobOfferController::class, 'index']);
    Route::get('job-offers/{jobOffer}', [JobOfferController::class, 'show']);

    // Escritura: solo empresa o admin.
    Route::middleware('role:company|admin')->group(function () {
        Route::post('job-offers', [JobOfferController::class, 'store']);
        Route::match(['put', 'patch'], 'job-offers/{jobOffer}', [JobOfferController::class, 'update']);
        Route::delete('job-offers/{jobOffer}', [JobOfferController::class, 'destroy']);
        Route::post('job-offers/{jobOffer}/image', [JobOfferController::class, 'uploadImage']);
        Route::delete('job-offers/{jobOffer}/image', [JobOfferController::class, 'deleteImage']);
    });

    /*
    |--------------------------------------------------------------------------
    | Perfil de empresa
    |--------------------------------------------------------------------------
    */
    Route::get('company-profiles', [CompanyProfileController::class, 'index']);
    Route::get('company-profiles/{userId}', [CompanyProfileController::class, 'show']);
    Route::middleware('role:company|admin')->group(function () {
        Route::post('company-profiles', [CompanyProfileController::class, 'store']);
        Route::put('company-profiles/{userId}', [CompanyProfileController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | Perfil de candidato
    |--------------------------------------------------------------------------
    */
    Route::get('candidate-profiles', [CandidateProfileController::class, 'index']);
    Route::get('candidate-profiles/{candidateProfile}', [CandidateProfileController::class, 'show']);
    Route::middleware('role:candidate|admin')->group(function () {
        Route::post('candidate-profiles', [CandidateProfileController::class, 'store']);
        Route::match(['put', 'patch'], 'candidate-profiles/{candidateProfile}', [CandidateProfileController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | CV del candidato autenticado — solo candidato o admin
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:candidate|admin')->group(function () {
        Route::get('candidate-cv', [CandidateCvController::class, 'show']);
        Route::post('candidate-cv', [CandidateCvController::class, 'upsert']);
        Route::post('cv-experiences', [CandidateCvController::class, 'storeExperience']);
        Route::put('cv-experiences/{experience}', [CandidateCvController::class, 'updateExperience']);
        Route::delete('cv-experiences/{experience}', [CandidateCvController::class, 'destroyExperience']);
        Route::post('cv-educations', [CandidateCvController::class, 'storeEducation']);
        Route::put('cv-educations/{education}', [CandidateCvController::class, 'updateEducation']);
        Route::delete('cv-educations/{education}', [CandidateCvController::class, 'destroyEducation']);
    });

    /*
    |--------------------------------------------------------------------------
    | Candidaturas
    |--------------------------------------------------------------------------
    */
    // Mis candidaturas (candidato) y candidaturas por oferta (empresa).
    // Deben ir ANTES de job-applications/{jobApplication}.
    Route::get('job-applications/my-candidatures', [JobApplicationController::class, 'myCandidatures'])->middleware('role:candidate|admin');
    Route::get('job-applications/by-offer/{offerId}', [JobApplicationController::class, 'byOffer'])->middleware('role:company|admin');

    // Lectura/creación/actualización: candidato y empresa comparten estas operaciones.
    Route::get('job-applications', [JobApplicationController::class, 'index']);
    Route::get('job-applications/{jobApplication}', [JobApplicationController::class, 'show']);
    Route::post('job-applications', [JobApplicationController::class, 'store']);
    Route::match(['put', 'patch'], 'job-applications/{jobApplication}', [JobApplicationController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| Rutas públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

// Ofertas públicas (solo PUBLISHED)
Route::get('public/job-offers', function (Request $request) {
    $query = \App\Models\JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality', 'media'])
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
    $offer = \App\Models\JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality', 'media'])
        ->findOrFail($id);
    return response()->json(['data' => $offer]);
});

Route::get('category-list', [CategoryController::class, 'getList']);
Route::get('disability-types', fn() => response()->json(\App\Models\DisabilityType::orderBy('name')->get()));
Route::get('job-categories', fn() => response()->json(\App\Models\JobCategory::orderBy('name')->get()));
Route::get('contract-types', fn() => response()->json(\App\Models\ContractType::orderBy('name')->get()));
Route::get('workday-types', fn() => response()->json(\App\Models\WorkdayType::orderBy('name')->get()));
Route::get('modality-types', fn() => response()->json(\App\Models\ModalityType::orderBy('name')->get()));
