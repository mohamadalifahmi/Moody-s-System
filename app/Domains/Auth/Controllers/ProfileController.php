<?php

namespace App\Domains\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        if ($request->filled('current_password') && $request->filled('password')) {
            $request->validate([
                'current_password' => 'required|current_password',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user->update(['password' => Hash::make($request->password)]);
        }

        session()->flash('success', 'تم تحديث الملف الشخصي بنجاح');

        return redirect()->route('profile');
    }
}
