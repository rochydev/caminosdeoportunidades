<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * GET /api/accounts?role=CANDIDATE&status=ACTIVE&sort=desc&per_page=15
     */
    public function index(Request $request)
    {
        $query = Account::with(['candidateProfile', 'companyProfile']);

        // Filtrar
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ordenar
        $sortDirection = $request->input('sort', 'desc');
        $query->orderBy('created_at', $sortDirection);

        // Paginar
        $perPage = $request->input('per_page', 15);
        $accounts = $query->paginate($perPage);

        // Ocultar password_hash
        $accounts->getCollection()->transform(function ($account) {
            return $account->makeHidden(['password_hash']);
        });

        return response()->json($accounts);
    }

    /**
     * GET /api/accounts/{id}
     */
    public function show(Account $account)
    {
        $account->load(['candidateProfile', 'companyProfile']);

        return response()->json([
            'success' => true,
            'data' => $account->makeHidden(['password_hash'])
        ]);
    }

    /**
     * POST /api/accounts
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['CANDIDATE', 'COMPANY', 'ADMIN'])],
            'email' => ['required', 'email', 'max:255', 'unique:accounts,email'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'BLOCKED'])],
        ]);

        $account = Account::create([
            'role' => $data['role'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'status' => $data['status'] ?? 'ACTIVE',
        ]);

        return response()->json([
            'success' => true,
            'data' => $account->makeHidden(['password_hash'])
        ], 201);
    }

    /**
     * PUT /api/accounts/{id}
     */
    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'role' => ['sometimes', Rule::in(['CANDIDATE', 'COMPANY', 'ADMIN'])],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('accounts', 'email')->ignore($account->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'BLOCKED'])],
        ]);

        $account->fill(collect($data)->except('password')->toArray());

        if (!empty($data['password'])) {
            $account->password_hash = Hash::make($data['password']);
        }

        $account->save();

        return response()->json([
            'success' => true,
            'data' => $account->makeHidden(['password_hash'])
        ]);
    }

    /**
     * DELETE /api/accounts/{id}
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return response()->json(null, 204);
    }
}