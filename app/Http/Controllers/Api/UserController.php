<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['candidateProfile', 'companyProfile', 'roles']);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orderColumn = $request->input('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'name', 'created_at', 'email'])) {
            $orderColumn = 'created_at';
        }

        $orderDirection = $request->input('order_direction', $request->input('sort', 'desc'));
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        if ($request->has('search_id')) {
            $query->where('id', $request->search_id);
        }

        if ($request->has('search_title')) {
            $query->where('name', 'like', '%'.$request->search_title.'%');
        }

        if ($request->has('search_global')) {
            $global = $request->search_global;
            $query->where(function ($q) use ($global) {
                $q->where('id', $global)
                    ->orWhere('name', 'like', '%'.$global.'%')
                    ->orWhere('email', 'like', '%'.$global.'%');
            });
        }

        $users = $query->orderBy($orderColumn, $orderDirection)
            ->paginate((int) $request->input('per_page', 15));

        $users->getCollection()->transform(function ($user) {
            return $user->makeHidden(['password']);
        });

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'surname1' => ['sometimes', 'string', 'max:255'],
            'surname2' => ['nullable', 'string', 'max:255'],
            'role' => ['sometimes', Rule::in(['CANDIDATE', 'COMPANY', 'ADMIN'])],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'BLOCKED'])],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $role = !empty($data['role_id']) ? Role::find($data['role_id']) : null;

        $user = User::create([
            'name' => $data['name'] ?? strstr($data['email'], '@', true),
            'surname1' => $data['surname1'] ?? '-',
            'surname2' => $data['surname2'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? null,
            'status' => $data['status'] ?? 'ACTIVE',
        ]);

        if ($role) {
            $user->assignRole($role);
        }

        return response()->json([
            'success' => true,
            'data' => $user->load('roles')->makeHidden(['password'])
        ], 201);
    }

    public function show(User $user)
    {
        $user->load(['candidateProfile', 'companyProfile', 'roles']);

        return response()->json([
            'success' => true,
            'data' => $user->makeHidden(['password'])
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'surname1' => ['sometimes', 'string', 'max:255'],
            'surname2' => ['nullable', 'string', 'max:255'],
            'role' => ['sometimes', Rule::in(['CANDIDATE', 'COMPANY', 'ADMIN'])],
            'status' => ['sometimes', Rule::in(['ACTIVE', 'BLOCKED'])],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $role = !empty($data['role_id']) ? Role::find($data['role_id']) : null;

        $user->fill(collect($data)->except(['password', 'role_id'])->toArray());

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if ($role) {
            $user->syncRoles($role);
        }

        return response()->json([
            'success' => true,
            'data' => $user->load('roles')->makeHidden(['password'])
        ]);
    }


    public function updateimg(Request $request)
    {
        $user = User::find($request->id);
      
        if($request->hasFile('picture')) {
            $user->media()->delete();
            $media = $user->addMediaFromRequest('picture')->preservingOriginal()->toMediaCollection('images-users');

        }
        $user =  User::with('media')->find($request->id);
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('user-delete');
        $user->delete();

        return response()->noContent();
    }
}
