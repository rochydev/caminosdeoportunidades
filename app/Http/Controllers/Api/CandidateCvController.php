<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CandidateCv;
use App\Models\CvExperience;
use App\Models\CvEducation;
use Illuminate\Http\Request;

class CandidateCvController extends Controller
{
    /** GET /api/candidate-cv — devuelve el CV del usuario autenticado */
    public function show(Request $request)
    {
        $cv = CandidateCv::with(['experiences' => fn($q) => $q->orderBy('start_date', 'desc'),
                                 'educations'  => fn($q) => $q->orderBy('start_date', 'desc')])
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json(['data' => $cv]);
    }

    /** POST /api/candidate-cv — crea o actualiza el CV (upsert) */
    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'sometimes|string|max:150',
            'summary'      => 'nullable|string',
            'skills'       => 'nullable|string',
            'languages'    => 'nullable|string',
            'availability' => 'nullable|in:FULL_TIME,PART_TIME,REMOTE_ONLY,HYBRID,FLEX',
        ]);

        $cv = CandidateCv::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        $cv->load(['experiences' => fn($q) => $q->orderBy('start_date', 'desc'),
                   'educations'  => fn($q) => $q->orderBy('start_date', 'desc')]);

        return response()->json(['data' => $cv]);
    }

    /* ── Experiencias ── */

    public function storeExperience(Request $request)
    {
        $validated = $request->validate([
            'company'     => 'required|string|max:150',
            'position'    => 'required|string|max:150',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $validated['cv_user_id'] = $request->user()->id;
        $exp = CvExperience::create($validated);

        return response()->json(['data' => $exp], 201);
    }

    public function updateExperience(Request $request, CvExperience $experience)
    {
        abort_if($experience->cv_user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'company'     => 'sometimes|string|max:150',
            'position'    => 'sometimes|string|max:150',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $experience->update($validated);
        return response()->json(['data' => $experience]);
    }

    public function destroyExperience(Request $request, CvExperience $experience)
    {
        abort_if($experience->cv_user_id !== $request->user()->id, 403);
        $experience->delete();
        return response()->noContent();
    }

    /* ── Formación ── */

    public function storeEducation(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:180',
            'degree'      => 'required|string|max:180',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $validated['cv_user_id'] = $request->user()->id;
        $edu = CvEducation::create($validated);

        return response()->json(['data' => $edu], 201);
    }

    public function updateEducation(Request $request, CvEducation $education)
    {
        abort_if($education->cv_user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'institution' => 'sometimes|string|max:180',
            'degree'      => 'sometimes|string|max:180',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $education->update($validated);
        return response()->json(['data' => $education]);
    }

    public function destroyEducation(Request $request, CvEducation $education)
    {
        abort_if($education->cv_user_id !== $request->user()->id, 403);
        $education->delete();
        return response()->noContent();
    }
}
