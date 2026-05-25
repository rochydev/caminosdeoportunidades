<?php

namespace App\Http\Controllers\Api;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */

    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'name', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        $users = User::
        when(request('search_id'), function ($query) {
            $query->where('id', request('search_id'));
        })
            ->when(request('search_title'), function ($query) {
                $query->where('name', 'like', '%'.request('search_title').'%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function($q) {
                    $q->where('id', request('search_global'))
                        ->orWhere('name', 'like', '%'.request('search_global').'%');

                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(500);

        return UserResource::collection($users);
    }

    // userswithtasks removed

    // usersfromgroup removed

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request)
    {
        $role = Role::find($request->role_id);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->surname1 = $request->surname1;
        $user->surname2 = $request->surname2;

        $user->password = Hash::make($request->password);

        if ($user->save()) {
            if ($role) {
                $user->assignRole($role);
            }
            return new UserResource($user);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return UserResource
     */
    public function show(User $user)
    {
        $user->load('roles');
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Solo el propio usuario, o un admin con permiso 'user-edit', puede editar.
        if (auth()->id() !== $user->id && ! auth()->user()->can('user-edit')) {
            abort(403, 'No autorizado para editar este usuario.');
        }

        $role = Role::find($request->role_id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->surname1 = $request->surname1;
        $user->surname2 = $request->surname2;

        if(!empty($request->password)) {
            $user->password = Hash::make($request->password) ?? $user->password;
        }
        if ($user->save()) {
            // Solo un admin (con 'user-edit') puede cambiar roles; así un usuario
            // editando su propio perfil no puede auto-ascenderse enviando role_id.
            if ($role && auth()->user()->can('user-edit')) {
                $user->syncRoles($role);
            }

            return new UserResource($user);
        }
    }


    public function updateimg(Request $request)
    {
        // Solo el propio usuario, o un admin con permiso 'user-edit', puede cambiar el avatar.
        if ((int) $request->id !== auth()->id() && ! auth()->user()->can('user-edit')) {
            abort(403, 'No autorizado.');
        }

        $user = User::find($request->id);

        if($request->hasFile('picture')) {
            $user->clearMediaCollection('images-users');
            $user->addMediaFromRequest('picture')->preservingOriginal()->toMediaCollection('images-users');
        }
        $user = User::with('media')->find($request->id);
        return new UserResource($user);
    }

    public function uploadCv(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf|max:10240',
            'id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->id);
        $user->addMediaFromRequest('cv')->toMediaCollection('cvs');

        return new UserResource($user->fresh());
    }

    public function deleteCv(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->id);
        $user->clearMediaCollection('cvs');

        return new UserResource($user->fresh());
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
