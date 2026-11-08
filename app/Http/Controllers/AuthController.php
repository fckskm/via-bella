<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Неверный адрес почты или пароль.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('account'));
    }

    public function showRegister()
    {
        return view('auth.register', ['interests' => config('via.interests')]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'interests' => ['nullable', 'array'],
            'interests.*' => ['string', 'in:' . implode(',', array_keys(config('via.interests')))],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user',
            'interests' => $data['interests'] ?? [],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('account'))->with('status', 'Добро пожаловать в VIA BELLA!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
