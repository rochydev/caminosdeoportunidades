<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobOfferController extends Controller
{
    /**
     * GET /api/job-offers?status=PUBLISHED&category_id=3&city=Barcelona&is_adapted=1&sort=desc&per_page=15
     */
    public function index(Request $request)
    {
        $query = JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality', 'tags', 'disabilities']);

        // Filtrar por status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por categoria
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrar por ciudad
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filtrar por tipos de contrato
        if ($request->has('contract_type_id')) {
            $query->where('contract_type_id', $request->contract_type_id);
        }

        // Filtrar por modalidad
        if ($request->has('modality_id')) {
            $query->where('modality_id', $request->modality_id);
        }

        // Filtrar por ofertas adaptadas
        if ($request->has('is_adapted')) {
            $query->where('is_adapted', $request->boolean('is_adapted'));
        }

        // Filtrar por empresa (usuari autenticado) - Por revisar, error: Undefined method 'id'
        if ($request->has('my_offers') && $request->my_offers) {
            // $query->where('company_user_id', auth()->id());
        }

        // Filtrar por tag
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        // Filtrar por tipos de discapacidad
        if ($request->has('disability_type_id')) {
            $query->whereHas('disabilities', function ($q) use ($request) {
                $q->where('disability_types.id', $request->disability_type_id);
            });
        }

        // Busca por titulo
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Ordenar
        $sortDirection = $request->input('sort', 'desc');
        $query->orderBy('created_at', $sortDirection);

        // Paginar
        $perPage = $request->input('per_page', 15);
        $offers = $query->paginate($perPage);

        return response()->json($offers);
    }

    /**
     * GET /api/job-offers/{id}
     */
    public function show(JobOffer $jobOffer)
    {
        $jobOffer->load(['company', 'category', 'contractType', 'workdayType', 'modality', 'tags', 'disabilities', 'applications']);

        return response()->json([
            'success' => true,
            'data' => $jobOffer
        ]);
    }

    /**
     * POST /api/job-offers
     */
    public function store(Request $request)
    {
        // $this->authorize('create', JobOffer::class);

        $data = $request->validate([
            'company_user_id' => ['required', 'integer', 'exists:users,id'],
            'category_id' => ['nullable', 'integer', 'exists:job_categories,id'],
            'contract_type_id' => ['nullable', 'integer', 'exists:contract_types,id'],
            'workday_type_id' => ['nullable', 'integer', 'exists:workday_types,id'],
            'modality_id' => ['nullable', 'integer', 'exists:modality_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'adaptations' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'is_adapted' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['DRAFT', 'PUBLISHED', 'CLOSED'])],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'disabilities' => ['nullable', 'array'],
            'disabilities.*' => ['exists:disability_types,id'],
        ]);

        $offer = JobOffer::create(collect($data)->except(['tags', 'disabilities'])->toArray());

        // Relacion N:M con tags (attach)
        if (!empty($data['tags'])) {
            $offer->tags()->attach($data['tags']);
        }

        // Relacion N:M con disabilities (attach)
        if (!empty($data['disabilities'])) {
            $offer->disabilities()->attach($data['disabilities']);
        }

        return response()->json([
            'success' => true,
            'data' => $offer->load(['tags', 'disabilities'])
        ], 201);
    }

    /**
     * PUT /api/job-offers/{id}
     */
    public function update(Request $request, JobOffer $jobOffer)
    {
        // Solo la empresa propietaria puede editar - error: Undefined method 'id'
        // if ($jobOffer->company_user_id !== auth()->id()) {
        //return response()->json(['success' => false, 'message' => 'No autoritzat'], 403);
        //}

        $data = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:job_categories,id'],
            'contract_type_id' => ['nullable', 'integer', 'exists:contract_types,id'],
            'workday_type_id' => ['nullable', 'integer', 'exists:workday_types,id'],
            'modality_id' => ['nullable', 'integer', 'exists:modality_types,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'requirements' => ['nullable', 'string'],
            'adaptations' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'is_adapted' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['DRAFT', 'PUBLISHED', 'CLOSED'])],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'disabilities' => ['nullable', 'array'],
            'disabilities.*' => ['exists:disability_types,id'],
        ]);

        $jobOffer->update(collect($data)->except(['tags', 'disabilities'])->toArray());

        // Relacion N:M con tags (sync remplaza los existentes)
        if (array_key_exists('tags', $data)) {
            $jobOffer->tags()->sync($data['tags'] ?? []);
        }

        // Relacion N:M con disabilities (sync remplaza los existentes)
        if (array_key_exists('disabilities', $data)) {
            $jobOffer->disabilities()->sync($data['disabilities'] ?? []);
        }

        return response()->json([
            'success' => true,
            'data' => $jobOffer->load(['tags', 'disabilities'])
        ]);
    }

    /**
     * DELETE /api/job-offers/{id}
     */
    public function destroy(JobOffer $jobOffer)
    {
        $jobOffer->delete();

        return response()->json(null, 204);
    }
}
