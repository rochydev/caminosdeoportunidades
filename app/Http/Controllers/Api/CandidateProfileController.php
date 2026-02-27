<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use Illuminate\Http\Request;

class CandidateProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = CandidateProfile::with(['user', 'disabilityType']);

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('disability_type_id')) {
            $query->where('disability_type_id', $request->disability_type_id);
        }

        if ($request->has('min_disability_degree')) {
            $query->where('disability_degree', '>=', $request->min_disability_degree);
        }

        $sortDirection = $request->input('sort', 'desc');
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        $query->orderBy('created_at', $sortDirection);

        $perPage = $request->input('per_page', 15);
        $profiles = $query->paginate($perPage);

        return response()->json($profiles);
    }

    public function show(CandidateProfile $candidateProfile)
    {
        $candidateProfile->load(['user', 'disabilityType']);

        return response()->json([
            'success' => true,
            'data' => $candidateProfile
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:candidate_profile,user_id'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:120'],
            'photo_url' => ['nullable', 'string', 'max:500'],
            'disability_type_id' => ['nullable', 'integer', 'exists:disability_type,id'],
            'disability_degree' => ['nullable', 'integer', 'min:0', 'max:100'],
            'accessibility_needs' => ['nullable', 'string', 'max:255'],
        ]);

        $profile = CandidateProfile::create($data);

        return response()->json([
            'success' => true,
            'data' => $profile
        ], 201);
    }

    public function update(Request $request, CandidateProfile $candidateProfile)
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:80'],
            'last_name' => ['sometimes', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:120'],
            'photo_url' => ['nullable', 'string', 'max:500'],
            'disability_type_id' => ['nullable', 'integer', 'exists:disability_type,id'],
            'disability_degree' => ['nullable', 'integer', 'min:0', 'max:100'],
            'accessibility_needs' => ['nullable', 'string', 'max:255'],
        ]);

        $candidateProfile->update($data);

        return response()->json([
            'success' => true,
            'data' => $candidateProfile
        ]);
    }
}
