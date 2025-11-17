<?php

namespace App\Http\Controllers;

use App\Models\Programacao;
use App\Models\User;
use Illuminate\Http\Request;

class InscricaoProgramacaoController extends Controller
{
    public function store(Request $request, Programacao $programacao)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // ✅ evita erro se já estiver inscrito na atividade
        $programacao->users()->syncWithoutDetaching([$user->id]);

        return redirect()
            ->back()
            ->with('success', 'Inscrição na atividade realizada com sucesso!');
    }

    public function destroy(Programacao $programacao, User $user)
    {
        $programacao->users()->detach($user->id);

        return redirect()
            ->back()
            ->with('success', 'Inscrição na atividade cancelada com sucesso!');
    }

    public function registrarPresenca(Request $request, Programacao $programacao)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // ✅ check-in: marca presente = true na pivot
        $programacao->users()->updateExistingPivot($user->id, ['presente' => true]);

        return redirect()
            ->back()
            ->with('success', 'Presença registrada com sucesso!');
    }

    public function removerPresenca(Request $request, Programacao $programacao)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // ✅ remove check-in: presente = false
        $programacao->users()->updateExistingPivot($user->id, ['presente' => false]);

        return redirect()
            ->back()
            ->with('success', 'Presença removida com sucesso!');
    }
}
