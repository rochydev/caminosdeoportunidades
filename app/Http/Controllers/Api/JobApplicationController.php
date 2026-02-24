<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobApplicationController extends Controller
{
    /**
    * GET /api/job-applications?status=SENT&offer_id=5&candidate_user_id=3&sort=desc&per_page=15
     */
    public function index(Request $request)
    {
        $query = JobApplication::with(['offer', 'candidate']);

        // Filtrar por status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        //  Filtrar por oferta
        if ($request->has('offer_id')) {
            $query->where('offer_id', $request->offer_id);
        }

        //  Filtrar por candidato
        if ($request->has('candidate_user_id')) {
            $query->where('candidate_user_id', $request->candidate_user_id);
        }

        // Filtrar aplicaciones del usuari autenticado (candidato) por revisar, error: Undefined method 'id'
        if ($request->has('my_applications') && $request->my_applications) {
            //$query->where('candidate_user_id', auth()->id());
        }

        // Filtrar por titulo de la oferta
        if ($request->has('offer_title')) {
            $query->whereHas('offer', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->offer_title . '%');
            });
        }

        // Ordenar
        $sortDirection = $request->input('sort', 'desc');
        $query->orderBy('created_at', $sortDirection);

        // Paginar
        $perPage = $request->input('per_page', 15);
        $applications = $query->paginate($perPage);

        return response()->json($applications);
    }

    /**
     * GET /api/job-applications/{id}
     */
    public function show(JobApplication $jobApplication)
    {

        $jobApplication->load(['offer', 'candidate']);

        return response()->json([
            'success' => true,
            'data' => $jobApplication
        ]);
    }

    /**
     * POST /api/job-applications
     */
    public function store(Request $request)
    {

        $data = $request->validate([
            'offer_id' => ['required', 'integer', 'exists:job_offers,id'],
            'candidate_user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['sometimes', Rule::in(['SENT', 'IN_REVIEW', 'ACCEPTED', 'REJECTED', 'CANCELED'])],
            'company_notes' => ['nullable', 'string'],
        ]);

        // Un candidato no puede aplicar dos veces a la misma oferta
        $exists = JobApplication::where('offer_id', $data['offer_id'])
            ->where('candidate_user_id', $data['candidate_user_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Ja has aplicat a aquesta oferta'
            ], 400);
        }

        $application = JobApplication::create($data);

        return response()->json([
            'success' => true,
            'data' => $application
        ], 201);
    }

    /**
     * PUT /api/job-applications/{id}
     */
    public function update(Request $request, JobApplication $jobApplication)
    {
        // Solo la empresa puede cambiar el status - Por revisar, error: Undefined method user
        //if (auth()->user()->role !== 'COMPANY') {
        //    return response()->json(['success' => false, 'message' => 'No autoritzat'], 403);
        //}

        $data = $request->validate([
            'status' => ['sometimes', Rule::in(['SENT', 'IN_REVIEW', 'ACCEPTED', 'REJECTED', 'CANCELED'])],
            'company_notes' => ['nullable', 'string'],
        ]);

        $jobApplication->update($data);

        return response()->json([
            'success' => true,
            'data' => $jobApplication
        ]);
    }

    /**
     * DELETE /api/job-applications/{id}
     */
    public function destroy(JobApplication $jobApplication)
    {
        $jobApplication->delete();

        return response()->json(null, 204);
    }
}
