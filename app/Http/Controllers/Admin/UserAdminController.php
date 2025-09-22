<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(20);
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|min:8|confirmed',
            'tipo_usuario' => ['required', Rule::in(['comum','admin','master'])],
            'ativo'        => 'boolean',
        ]);
        $data['password'] = bcrypt($data['password']);
        User::create($data);

        return redirect()->route('admin.usuarios.index')->with('success','Usuário criado.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $r, User $usuario)
    {
        $data = $r->validate([
            'name'         => 'required|string|max:255',
            'email'        => ['required','email', Rule::unique('users')->ignore($usuario->id)],
            'tipo_usuario' => ['required', Rule::in(['comum','admin','master'])],
            'ativo'        => 'boolean',
            'password'     => 'nullable|min:8|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return back()->with('success','Atualizado.');
    }

    public function ativar(User $user)
    {
        $user->update(['ativo' => 1]);
        return back()->with('success','Ativado.');
    }

    public function desativar(User $user)
    {
        $user->update(['ativo' => 0]);
        return back()->with('success','Desativado.');
    }

    public function alterarTipo(Request $r, User $user)
    {
        $r->validate(['tipo' => ['required', Rule::in(['comum','admin','master'])]]);
        $user->update(['tipo_usuario' => $r->tipo]);
        return back()->with('success','Tipo alterado.');
    }
}
