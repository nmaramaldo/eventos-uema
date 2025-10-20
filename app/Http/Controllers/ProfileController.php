<?php

namespace App\Http\Controllers;

use App\Rules\Cpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Exibe formulário.
     */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Atualiza informações básicas do perfil.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // normaliza/limpa máscara do CPF antes da validação única
        $cpfRaw = preg_replace('/\D/', '', (string) $request->input('cpf'));

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'cpf'   => [
                'nullable',
                'string',
                'size:11',
                new Cpf,
                Rule::unique('users', 'cpf')->ignore($user->id),
            ],
        ], [], [
            'name'  => 'nome',
            'email' => 'e-mail',
            'cpf'   => 'CPF',
        ]);

        // garante persistência do CPF sem máscara
        $validated['cpf'] = $cpfRaw ?: null;

        $user->fill($validated);

        // Se o e-mail mudou, invalida a verificação (mantém seu comportamento padrão se aplicável)
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Remove a conta do usuário.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [], ['password' => 'senha']);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Conta excluída com sucesso.');
    }
}
