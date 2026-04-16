<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\CompanyProfile;
use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function stats()
    {
        return response()->json([
            'users' => User::count(),
            'candidates' => User::role('candidate')->count(),
            'companies' => User::role('company')->count(),
            'offers' => JobOffer::count(),
            'offers_published' => JobOffer::where('status', 'PUBLISHED')->count(),
            'offers_draft' => JobOffer::where('status', 'DRAFT')->count(),
            'offers_closed' => JobOffer::where('status', 'CLOSED')->count(),
            'applications' => Application::count(),
            'applications_sent' => Application::where('status', 'SENT')->count(),
            'applications_accepted' => Application::where('status', 'ACCEPTED')->count(),
            'applications_rejected' => Application::where('status', 'REJECTED')->count(),
        ]);
    }

    // ── Empresas ──

    public function companies(Request $request)
    {
        $query = CompanyProfile::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name', 'like', "%$s%")
                  ->orWhere('sector', 'like', "%$s%")
                  ->orWhere('city', 'like', "%$s%");
            });
        }

        if ($request->filled('validation_status')) {
            $query->where('validation_status', $request->validation_status);
        }

        $query->orderBy('created_at', 'desc');

        return response()->json($query->paginate($request->input('per_page', 50)));
    }

    public function companyShow($userId)
    {
        $company = CompanyProfile::with('user')->where('user_id', $userId)->firstOrFail();

        $offersCount = JobOffer::where('company_user_id', $userId)->count();
        $applicationsCount = Application::whereHas('offer', fn($q) => $q->where('company_user_id', $userId))->count();

        return response()->json([
            'data' => $company,
            'offers_count' => $offersCount,
            'applications_count' => $applicationsCount,
        ]);
    }

    public function companyUpdateStatus(Request $request, $userId)
    {
        $data = $request->validate([
            'validation_status' => ['required', 'in:PENDING,VALIDATED,REJECTED,BLOCKED'],
        ]);

        $company = CompanyProfile::where('user_id', $userId)->firstOrFail();
        $company->update($data);

        return response()->json(['success' => true, 'data' => $company->fresh()]);
    }

    // ── Ofertas ──

    public function offers(Request $request)
    {
        $query = JobOffer::with(['company', 'category', 'contractType', 'workdayType', 'modality'])
            ->withCount('applications');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_user_id')) {
            $query->where('company_user_id', $request->company_user_id);
        }

        if ($request->filled('is_adapted')) {
            $query->where('is_adapted', $request->boolean('is_adapted'));
        }

        $query->orderBy('created_at', 'desc');

        return response()->json($query->paginate($request->input('per_page', 50)));
    }

    public function offerUpdateStatus(Request $request, JobOffer $offer)
    {
        $data = $request->validate([
            'status' => ['required', 'in:DRAFT,PUBLISHED,CLOSED'],
        ]);

        $offer->update($data);

        return response()->json(['success' => true, 'data' => $offer->fresh()]);
    }

    public function offerDestroy(JobOffer $offer)
    {
        $offer->delete();
        return response()->noContent();
    }

    // ── Candidaturas ──

    public function applications(Request $request)
    {
        $query = Application::with(['offer.company', 'candidate']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('candidate', fn($cq) => $cq->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"))
                  ->orWhereHas('offer', fn($oq) => $oq->where('title', 'like', "%$s%"));
            });
        }

        $query->orderBy('created_at', 'desc');

        return response()->json($query->paginate($request->input('per_page', 50)));
    }

    public function applicationUpdateStatus(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => ['required', 'in:SENT,IN_REVIEW,ACCEPTED,REJECTED,CANCELED'],
        ]);

        $application->update($data);

        return response()->json(['success' => true, 'data' => $application->fresh()]);
    }
}
