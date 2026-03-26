<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    public function index(Request $request)
    {
        $query = JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality']);

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('contract_type_id')) {
            $query->where('contract_type_id', $request->contract_type_id);
        }

        if ($request->has('workday_type_id')) {
            $query->where('workday_type_id', $request->workday_type_id);
        }

        if ($request->has('modality_id')) {
            $query->where('modality_id', $request->modality_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('company_user_id')) {
            $query->where('company_user_id', $request->company_user_id);
        }

        if ($request->has('is_adapted')) {
            $query->where('is_adapted', $request->boolean('is_adapted'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $sortDirection = $request->input('sort', 'desc');
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        $query->orderBy('created_at', $sortDirection);

        $perPage = $request->input('per_page', 15);
        $offers = $query->paginate($perPage);

        return response()->json($offers);
    }

    public function show(JobOffer $jobOffer)
    {
        $jobOffer->load(['company', 'category', 'contractType', 'workdayType', 'modality', 'tags', 'disabilities']);

        return response()->json([
            'success' => true,
            'data' => $jobOffer
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_user_id' => ['required', 'integer', 'exists:users,id'],
            'category_id' => ['nullable', 'integer', 'exists:job_category,id'],
            'contract_type_id' => ['nullable', 'integer', 'exists:contract_type,id'],
            'workday_type_id' => ['nullable', 'integer', 'exists:workday_type,id'],
            'modality_id' => ['nullable', 'integer', 'exists:modality_type,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'adaptations' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'is_adapted' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:DRAFT,PUBLISHED,CLOSED'],
        ]);

        $offer = JobOffer::create($data);

        return response()->json([
            'success' => true,
            'data' => $offer
        ], 201);
    }

    public function update(Request $request, JobOffer $jobOffer)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:job_category,id'],
            'contract_type_id' => ['nullable', 'integer', 'exists:contract_type,id'],
            'workday_type_id' => ['nullable', 'integer', 'exists:workday_type,id'],
            'modality_id' => ['nullable', 'integer', 'exists:modality_type,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'requirements' => ['nullable', 'string'],
            'adaptations' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'is_adapted' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:DRAFT,PUBLISHED,CLOSED'],
        ]);

        $jobOffer->update($data);

        return response()->json([
            'success' => true,
            'data' => $jobOffer
        ]);
    }

    public function destroy(JobOffer $jobOffer)
    {
        $jobOffer->delete();

        return response()->noContent();
    }

    public function recommended(Request $request)
    {
        $candidateId = $request->input('candidate_id') ?? auth()->id();

        // Obtener ofertas a las que ya ha aplicado
        $appliedOfferIds = \DB::table('application')
            ->where('candidate_user_id', $candidateId)
            ->get()
            ->map(function ($record) {
                return $record->offer_id;
            })
            ->toArray();

        // Construir consulta base
        $query = JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality'])
            ->where('status', 'PUBLISHED')
            ->whereNotIn('id', $appliedOfferIds);


        // Ordenar por más reciente
        $sortDirection = $request->input('sort', 'desc');
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        $query->orderBy('created_at', $sortDirection);

        $perPage = $request->input('per_page', 3);
        $offers = $query->paginate($perPage);

        // Transformar datos para consistencia con el frontend
        $data = collect($offers->items())->map(function ($offer) {
            return [
                'id' => $offer->id,
                'title' => $offer->title,
                'description' => $offer->description,
                'city' => $offer->city,
                'status' => $offer->status,
                'salary' => $offer->salary ?? null,
                'company' => $offer->company,
                'category' => $offer->category,
                'contractType' => $offer->contractType,
                'workdayType' => $offer->workdayType,
                'modality' => $offer->modality
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data->toArray(),
            'pagination' => [
                'total' => $offers->total(),
                'per_page' => $offers->perPage(),
                'current_page' => $offers->currentPage(),
                'last_page' => $offers->lastPage(),
            ]
        ]);
    }
}
