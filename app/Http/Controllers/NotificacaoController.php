<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotificacaoRequest;
use App\Http\Requests\UpdateNotificacaoRequest;
use App\Models\Notificacao;
use App\Models\User;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificacoes = Notificacao::with('user')->latest()->get();

        return view('notificacoes.index', compact('notificacoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();

        return view('notificacoes.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotificacaoRequest $request)
    {
        $data = $request->validated();
        $data['enviado_em'] = now();

        Notificacao::create($data);

        return redirect()->route('notificacoes.index')->with('success', 'Notificação enviada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Notificacao $notificacao)
    {
        return view('notificacoes.show', compact('notificacao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notificacao $notificacao)
    {
        $users = User::all();

        return view('notificacoes.edit', compact('notificacao', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotificacaoRequest $request, Notificacao $notificacao)
    {
        $notificacao->update($request->validated());

        return redirect()->route('notificacoes.index')->with('success', 'Notificação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notificacao $notificacao)
    {
        $notificacao->delete();

        return redirect()->route('notificacoes.index')->with('success', 'Notificação removida com sucesso');
    }

    public function marcarComoLida(Notificacao $notificacao)
    {
        $notificacao->update(['lido' => true ]);

        return redirect()->back()->with('success', 'Notificação marcada como lida!');
    }
}
