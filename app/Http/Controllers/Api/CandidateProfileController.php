<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use Illuminate\Http\Request;

class CandidateProfileController extends Controller
{
    /**
     * GET /api/candidate-profiles?city=Barcelona&disability_type_id=1&sort=asc&per_page=15
     */
    public function index(Request $request)
    {
        $query = CandidateProfile::with(['user', 'disabilityType']);

        // Filtrar por ciudad
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filtrar por tipos de discapacidad
        if ($request->has('disability_type_id')) {
            $query->where('disability_type_id', $request->disability_type_id);
        }

        // Filtrar por grado de discapacidad minima
        if ($request->has('min_disability_degree')) {
            $query->where('disability_degree', '>=', $request->min_disability_degree);
        }

        // Ordenar
        $sortDirection = $request->input('sort', 'desc');
        $query->orderBy('created_at', $sortDirection);

        // Paginar
        $perPage = $request->input('per_page', 15);
        $profiles = $query->paginate($perPage);

        return response()->json($profiles);
    }

    /**
      * GET /api/candidate-profiles/{user_id}
     */
    public function show(CandidateProfile $candidateProfile)
    {
          $candidateProfile->load(['user', 'disabilityType']);

        return response()->json([
            'success' => true,
            'data' => $candidateProfile
        ]);
    }

    /**
     * POST /api/candidate-profiles
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:candidate_profiles,user_id'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:120'],
            'photo_url' => ['nullable', 'string', 'max:500'],
            'disability_type_id' => ['nullable', 'integer', 'exists:disability_types,id'],
            'disability_degree' => ['nullable', 'integer', 'min:0', 'max:100'],
            'accessibility_needs' => ['nullable', 'string', 'max:255'],
        ]);

        $profile = CandidateProfile::create($data);

        return response()->json([
            'success' => true,
            'data' => $profile
        ], 201);
    }

    /**
    * PUT /api/candidate-profiles/{user_id}
     */
    public function update(Request $request, CandidateProfile $candidateProfile)
    {
        // Solo el propio usuario puede editar su perfil - Error: Undefined method id
        // if ($candidateProfile->user_id !== auth()->id()) {
        //     return response()->json(['success' => false, 'message' => 'No autoritzat'], 403);
        // }

        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:80'],
            'last_name' => ['sometimes', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:120'],
            'photo_url' => ['nullable', 'string', 'max:500'],
            'disability_type_id' => ['nullable', 'integer', 'exists:disability_types,id'],
            'disability_degree' => ['nullable', 'integer', 'min:0', 'max:100'],
            'accessibility_needs' => ['nullable', 'string', 'max:255'],
        ]);

        $candidateProfile->update($data);

        return response()->json([
            'success' => true,
            'data' => $candidateProfile
        ]);
    }

    /**
    * DELETE /api/candidate-profiles/{user_id}
     */
    public function destroy(CandidateProfile $candidateProfile)
    {
        $candidateProfile->delete();

        return response()->json(null, 204);
    }
}
