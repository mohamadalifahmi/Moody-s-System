<?php

namespace App\Domains\Auth\Controllers;

use App\Domains\Auth\Models\ActivityLog;
use App\Domains\Auth\Models\Tenant;
use App\Domains\Auth\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        Auth::login($user);
        session(['tenant_id' => $user->tenant_id]);

        return redirect()->route('dashboard');
    }

    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'restaurant_name' => ['required', 'string', 'max:255'],
            'restaurant_slug' => ['required', 'string', 'max:255', 'unique:tenants,slug'],
        ]);
    }

    protected function create(array $data): User
    {
        $tenant = Tenant::create([
            'name' => $data['restaurant_name'],
            'slug' => $data['restaurant_slug'],
            'email' => $data['email'],
            'business_type' => 'general',
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tenant_id' => $tenant->id,
            'role' => 'admin',
            'is_active' => true,
        ]);

        ActivityLog::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'action' => 'tenant_created',
            'description' => 'New business registered: ' . $tenant->name,
            'subject_type' => get_class($tenant),
            'subject_id' => $tenant->id,
            'properties' => [
                'restaurant_name' => $data['restaurant_name'],
                'restaurant_slug' => $data['restaurant_slug'],
            ],
        ]);

        return $user;
    }
}
