<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['offer', 'candidate']);

        if ($request->has('offer_id')) {
            $query->where('offer_id', $request->offer_id);
        }

        if ($request->has('candidate_user_id')) {
            $query->where('candidate_user_id', $request->candidate_user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sortDirection = $request->input('sort', 'desc');
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        $query->orderBy('created_at', $sortDirection);

        $perPage = $request->input('per_page', 15);
        $applications = $query->paginate($perPage);

        return response()->json($applications);
    }

    public function show(Application $jobApplication)
    {
        $jobApplication->load(['offer', 'candidate']);

        return response()->json([
            'success' => true,
            'data' => $jobApplication
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'offer_id' => ['required', 'integer', 'exists:job_offer,id'],
            'candidate_user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:SENT,IN_REVIEW,ACCEPTED,REJECTED,CANCELED'],
            'company_notes' => ['nullable', 'string'],
        ]);

        $existing = Application::where('offer_id', $data['offer_id'])
            ->where('candidate_user_id', $data['candidate_user_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una solicitud para esta oferta'
            ], 422);
        }

        $application = Application::create($data);

        return response()->json([
            'success' => true,
            'data' => $application
        ], 201);
    }

    public function update(Request $request, Application $jobApplication)
    {
        $data = $request->validate([
            'status' => ['sometimes', 'in:SENT,IN_REVIEW,ACCEPTED,REJECTED,CANCELED'],
            'company_notes' => ['nullable', 'string'],
        ]);

        $jobApplication->update($data);

        return response()->json([
            'success' => true,
            'data' => $jobApplication
        ]);
    }
}
