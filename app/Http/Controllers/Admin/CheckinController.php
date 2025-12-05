<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Inscricao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckinController extends Controller
{
    /**
     * Mostra a view com a câmera para fazer o check-in via QR Code.
     */
    public function scanner(Event $evento)
    {
        $this->authorize('update', $evento);

        return view('admin.checkin.scanner', compact('evento'));
    }

    /**
     * Processa o check-in a partir do ID da inscrição (UUID) vindo do QR Code.
     * Rota de API: POST /api/checkin
     */
    public function processCheckin(Request $request)
    {
        $request->validate([
            'inscricao_id' => 'required|string|exists:inscricoes,id',
            'evento_id' => 'required|string|exists:eventos,id',
        ]);

        $inscricaoId = $request->input('inscricao_id');
        $eventoId = $request->input('evento_id');

        $inscricao = Inscricao::with(['user', 'evento'])->find($inscricaoId);

        // Validações
        if (!$inscricao) {
            return response()->json([
                'status' => 'error',
                'message' => 'Inscrição não encontrada.',
            ], 404);
        }
        
        if ($inscricao->evento_id !== $eventoId) {
            return response()->json([
                'status' => 'error',
                'message' => "Esta inscrição pertence ao evento '{$inscricao->evento->nome}', não ao evento atual.",
            ], 422); // Unprocessable Entity
        }

        if ($inscricao->presente) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Check-in já foi realizado anteriormente para este participante.',
                'data' => [
                    'participante' => $inscricao->user->name,
                    'horario_checkin' => $inscricao->updated_at->toIso8601String(),
                ]
            ]);
        }

        // Realiza o check-in
        $inscricao->presente = true;
        $inscricao->save();
        
        Log::info("Check-in realizado com sucesso para o participante {$inscricao->user->name} (ID: {$inscricao->user_id}) no evento {$inscricao->evento->nome} (ID: {$inscricao->evento_id})");

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in realizado com sucesso!',
            'data' => [
                'participante' => $inscricao->user->name,
                'horario_checkin' => $inscricao->updated_at->toIso8601String(),
            ]
        ]);
    }
}
