<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobOffer;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['offer', 'candidate']);

        // Cada uno solo ve lo suyo: el candidato sus candidaturas, la empresa
        // las de sus propias ofertas, y el admin todas.
        $user = $request->user();
        if (! $user->hasRole('admin')) {
            if ($user->hasRole('company')) {
                $query->whereHas('offer', fn($q) => $q->where('company_user_id', $user->id));
            } else {
                $query->where('candidate_user_id', $user->id);
            }
        }

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

        // El candidato siempre es el usuario autenticado (evita postular en nombre de otro).
        if (! $request->user()->hasRole('admin')) {
            $data['candidate_user_id'] = $request->user()->id;
        }

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
        // Solo el candidato dueño, la empresa dueña de la oferta o un admin pueden modificarla.
        $user = $request->user();
        $esCandidatoDuenyo = $jobApplication->candidate_user_id === $user->id;
        $esEmpresaDuenya = $jobApplication->offer?->company_user_id === $user->id;
        abort_if(! $esCandidatoDuenyo && ! $esEmpresaDuenya && ! $user->hasRole('admin'), 403);

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

    public function byOffer(Request $request, $offerId)
    {
        // Una empresa solo puede ver las candidaturas de SUS propias ofertas (admin todas).
        $offer = JobOffer::findOrFail($offerId);
        abort_if($offer->company_user_id !== $request->user()->id && ! $request->user()->hasRole('admin'), 403);

        $applications = Application::where('offer_id', $offerId)
            ->with([
                'candidate' => function ($q) {
                    $q->with(['candidateProfile', 'media']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 50));

        $data = $applications->map(function ($application) {
            $candidate = $application->candidate;
            $cvMedia   = $candidate ? $candidate->getFirstMedia('cvs') : null;

            return [
                'id'            => $application->id,
                'status'        => $application->status,
                'company_notes' => $application->company_notes,
                'created_at'    => $application->created_at,
                'candidate'     => $candidate ? [
                    'id'       => $candidate->id,
                    'name'     => $candidate->name,
                    'surname1' => $candidate->surname1,
                    'email'    => $candidate->email,
                    'avatar'   => $candidate->getFirstMediaUrl('images-users') ?: null,
                    'cv'       => $cvMedia ? [
                        'name' => $cvMedia->file_name,
                        'url'  => $cvMedia->getUrl(),
                    ] : null,
                    'profile'  => $candidate->candidateProfile ? [
                        'phone'            => $candidate->candidateProfile->phone,
                        'city'             => $candidate->candidateProfile->city,
                        'disability_degree' => $candidate->candidateProfile->disability_degree,
                    ] : null,
                ] : null,
            ];
        });

        return response()->json([
            'success'    => true,
            'data'       => $data->values()->toArray(),
            'pagination' => [
                'total'        => $applications->total(),
                'per_page'     => $applications->perPage(),
                'current_page' => $applications->currentPage(),
                'last_page'    => $applications->lastPage(),
            ],
        ]);
    }

    public function myCandidatures(Request $request)
    {
        // Un candidato solo ve SUS candidaturas; un admin puede consultar las de otro.
        $candidateId = $request->user()->hasRole('admin')
            ? ($request->input('candidate_id') ?? auth()->id())
            : auth()->id();

        $applications = Application::where('candidate_user_id', $candidateId)
            ->with([
                'offer' => function ($query) {
                    $query->with(['company', 'category', 'contractType', 'workdayType', 'modality']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        // Transformar datos para que el frontend reciba la oferta con la info de aplicación
        $data = $applications->map(function ($application) {
            return [
                'id' => $application->offer->id,
                'title' => $application->offer->title,
                'description' => $application->offer->description,
                'city' => $application->offer->city,
                'status' => $application->offer->status,
                'salary' => $application->offer->salary ?? null,
                'company' => $application->offer->company,
                'category' => $application->offer->category,
                'contractType' => $application->offer->contractType,
                'workdayType' => $application->offer->workdayType,
                'modality' => $application->offer->modality,
                'application_id' => $application->id,
                'application_status' => $application->status
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data->toArray(),
            'pagination' => [
                'total' => $applications->total(),
                'per_page' => $applications->perPage(),
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
            ]
        ]);
    }
}
