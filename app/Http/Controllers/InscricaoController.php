<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscricaoRequest;
use App\Http\Requests\UpdateInscricaoRequest;
use App\Models\Event;
use App\Models\Inscricao;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inscricoes = Inscricao::with(['event'])
            ->where('user_id', auth()->guard('web')->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inscricoes.index', compact('inscricoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eventos = Event::where('data', '>=', now())
            ->orderBy('data', 'asc')
            ->get();

        return view('inscricoes.create', compact('eventos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInscricaoRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->guard('web')->id();

        try {
            $existingInscricao = Inscricao::where('user_id', $validated['user_id'])

                ->where('event_id', $validated['event_id'])
                ->first();

            if ($existingInscricao) {
                return back()->withErrors(['error' => 'Você já está inscrito neste evento.']);
            }

            $inscricao = Inscricao::create($validated);

            return redirect()->route('inscricoes.index')->with('success', 'Inscrição criada com sucesso');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['error' => 'Você já está inscrito neste evento.']);
            }
            return back()->withErrors(['error' => 'Erro ao processar a inscrição']);
        } catch (Exception $e) {

            return back()->withErrors(['error' => 'Erro ao processar a inscrição']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        $inscricao->load(['user', 'event']);
        return view('inscricoes.show', compact('inscricao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        $eventos = Event::where('data', '>=', now())->get();
        return view('inscricoes.edit', compact('inscricao', 'eventos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInscricaoRequest $request, Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        $inscricao->update($request->validated());

        return redirect()->route('inscricoes.index')->with('success', 'Inscrição atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        $inscricao->delete();

        return redirect()->route('inscricoes.index')->with('success', 'Inscrição deletada com sucesso!');
    }
}
