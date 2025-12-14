<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscricaoRequest;
use App\Http\Requests\UpdateInscricaoRequest;
use App\Models\Event;
use App\Models\Inscricao;
use App\Models\CertificadoModelo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    /**
     * Minhas inscrições (usuário logado).
     */
    public function index()
    {
        $inscricoes = Inscricao::with('evento')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('inscricoes.index', compact('inscricoes'));
    }

    /**
     * Inscrever o usuário logado em um evento.
     */
    public function store(StoreInscricaoRequest $request)
    {
        $evento = Event::findOrFail($request->input('evento_id'));

        // ✅ AUTORIZAÇÃO: Chama o método 'create' da InscricaoPolicy.
        $this->authorize('create', Inscricao::class);

        // Validações de negócio que você já tinha (estão ótimas!)
        if (!$evento->inscricoesAbertas()) {
            return back()->with('error', 'As inscrições para este evento não estão abertas.');
        }
        if ($evento->vagasDisponiveis() !== null && $evento->vagasDisponiveis() <= 0) {
            return back()->with('error', 'Não há vagas disponíveis para este evento.');
        }

        // Tenta criar a inscrição (ou a recupera se já existir)
        $inscricao = Inscricao::firstOrCreate(
            ['user_id' => auth()->id(), 'evento_id' => $evento->id],
            ['status' => 'ativa'] // Será ignorado se a inscrição já existir
        );

        // Verifica se a inscrição foi criada nesta requisição
        if ($inscricao->wasRecentlyCreated) {
            return redirect()->route('inscricoes.index')->with('success', 'Inscrição realizada com sucesso!');
        }

        return redirect()->route('inscricoes.index')->with('info', 'Você já estava inscrito neste evento.');
    }

    /**
     * Ver detalhes de uma inscrição específica.
     */
    public function show(Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'view' da InscricaoPolicy.
        $this->authorize('view', $inscricao);
        
        $inscricao->load(['evento']);
        return view('inscricoes.show', compact('inscricao'));
    }

    /**
     * Mostra o formulário para editar uma inscrição.
     */
    public function edit(Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'update' da InscricaoPolicy.
        $this->authorize('update', $inscricao);
        
        return view('inscricoes.edit', compact('inscricao'));
    }

    /**
     * Atualiza uma inscrição.
     */
    public function update(UpdateInscricaoRequest $request, Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'update' da InscricaoPolicy.
        $this->authorize('update', $inscricao);
        
        $inscricao->update($request->validated());
        return redirect()->route('inscricoes.index')->with('success', 'Inscrição atualizada com sucesso!');
    }

    /**
     * Cancelar (deletar) uma inscrição.
     */
    public function destroy(Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'delete' da InscricaoPolicy.
        $this->authorize('delete', $inscricao);
        
        $inscricao->delete();
        
        return redirect()->route('inscricoes.index')->with('success', 'Inscrição cancelada com sucesso!');
    }

    /**
     * Mostra o QR Code para check-in do participante.
     */
    public function showQrCode(Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'view' da InscricaoPolicy.
        $this->authorize('view', $inscricao);

        return view('inscricoes.qrcode', compact('inscricao'));
    }

    /**
     * 🔹 Tela de credenciamento (check-in geral do evento)
     * Lista todos os inscritos do evento para o coordenador/admin.
     */
    public function checkinEvento(Event $evento)
    {
        // Quem pode credenciar? Mesma regra de "update" do evento.
        $this->authorize('update', $evento);

        $inscricoes = Inscricao::with('user')
            ->where('evento_id', $evento->id)
            ->orderBy('data_inscricao')
            ->paginate(50);

        // Adicionar esta linha para buscar os modelos
        $modelos = CertificadoModelo::doEvento($evento->id)
            ->publicados()
            ->orderBy('titulo')
            ->get();

        return view('eventos.checkin', compact('evento', 'inscricoes', 'modelos'));
    }

    /**
     * 🔹 Alterna o status de presença de uma inscrição nesse evento.
     */
    public function toggleCheckinEvento(Event $evento, Inscricao $inscricao, Request $request)
    {
        $this->authorize('update', $evento);

        // Segurança extra: garantir que essa inscrição é do evento certo
        if ($inscricao->evento_id !== $evento->id) {
            abort(404);
        }

        $inscricao->presente = !$inscricao->presente;
        $inscricao->save();

        return redirect()
            ->back()
            ->with(
                'success',
                $inscricao->presente ? 'Check-in realizado com sucesso!' : 'Check-in removido com sucesso!'
            );
    }

    public function autoCheckin(Request $request, Event $evento)
    {
        // 1. Verifica se o link é válido e não expirou (Segurança)
        if (! $request->hasValidSignature()) {
            abort(403, 'Este QR Code expirou ou é inválido.');
        }

        $user = auth()->user();

        // 2. Busca a inscrição
        $inscricao = Inscricao::where('evento_id', $evento->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$inscricao) {
            return redirect()->route('app.home')
                ->with('error', 'Você não está inscrito neste evento.');
        }

        // 3. Registra a presença
        $inscricao->update([
            'presente' => true,
            'checkin_at' => now()
        ]);

        return redirect()->route('meus-eventos.index')
            ->with('success', "Check-in realizado com sucesso em: {$evento->nome}!");
    }
    
}
