<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // 🧪 Validate input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 👤 Create user WITH role (this fixes your error)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'farmer', // ✅ REQUIRED FIX
        ]);

        // 🎭 Assign role (if using Spatie)
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('farmer');
        }

        // 🌱 Create farmer profile
        Farmer::create([
            'user_id' => $user->id,
            'first_name' => $request->name,
            'last_name' => '',
        ]);

        // 🔔 Fire registered event
        event(new Registered($user));

        // 🔐 Log user in
        Auth::login($user);

        // 🚀 Redirect to farm creation
        return redirect()->route('farms.create');
    }
}