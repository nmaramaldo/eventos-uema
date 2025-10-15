<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Rules\Cpf;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            // sem 'lowercase' para evitar erro em versões antigas
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['required', new Cpf],
        ]);

        $name  = trim($validated['name']);
        $email = mb_strtolower(trim($validated['email']), 'UTF-8');

        $user = User::create([
            'name'         => $name,
            'email'        => $email,
            'password'     => Hash::make($validated['password']),
            'tipo_usuario' => 'comum',
            'ativo'        => true,
            'cpf' => $request->cpf,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
