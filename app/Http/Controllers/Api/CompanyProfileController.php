<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyProfile::with('user');

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('sector')) {
            $query->where('sector', 'like', '%' . $request->sector . '%');
        }

        if ($request->has('validation_status')) {
            $query->where('validation_status', $request->validation_status);
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

    public function show($userId)
    {
        $profile = CompanyProfile::with('user')->where('user_id', $userId)->first();

        if (!$profile) {
            return response()->json(['success' => false, 'data' => null], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $profile
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:company_profile,user_id'],
            'company_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sector' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'offers_adapted_positions' => ['nullable', 'boolean'],
            'offers_remote_work' => ['nullable', 'boolean'],
            'offers_reasonable_adjustments' => ['nullable', 'boolean'],
        ]);

        $data['validation_status'] = 'PENDING';

        $profile = CompanyProfile::create($data);

        return response()->json([
            'success' => true,
            'data' => $profile
        ], 201);
    }

    public function update(Request $request, $userId)
    {
        $profile = CompanyProfile::where('user_id', $userId)->firstOrFail();

        $data = $request->validate([
            'company_name' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sector' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'offers_adapted_positions' => ['nullable', 'boolean'],
            'offers_remote_work' => ['nullable', 'boolean'],
            'offers_reasonable_adjustments' => ['nullable', 'boolean'],
        ]);

        $profile->update($data);

        return response()->json([
            'success' => true,
            'data' => $profile->fresh()
        ]);
    }
}
