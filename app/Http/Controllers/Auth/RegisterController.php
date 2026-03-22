<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\CompanyProfile;
use App\Models\CandidateProfile;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'surname1' => ['required', 'string', 'max:255'],
            'surname2' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_type' => ['nullable', 'string', 'in:candidate,company'],
            'company_name' => ['required_if:role_type,company', 'string', 'max:255'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'surname1' => $data['surname1'],
            'surname2' => $data['surname2'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 'ACTIVE',
        ]);

        $roleType = $data['role_type'] ?? 'candidate';
        $role = Role::where('name', $roleType)->first();
        
        if ($role) {
            $user->assignRole($role);
        }

        if ($roleType === 'company') {
            CompanyProfile::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'validation_status' => 'PENDING',
            ]);
        } else {
            CandidateProfile::create([
                'user_id' => $user->id,
                'first_name' => $data['name'],
                'last_name' => trim($data['surname1'] . ' ' . ($data['surname2'] ?? '')),
            ]);
        }

        return $user;
    }
}
