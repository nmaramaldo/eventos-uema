<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;

class MyEventsController extends Controller
{
    public function index()
    {
        //
    }

    public function edit(Event $evento)
    {
        //
    }

    public function jornada()
    {
        $user = Auth::user();
        $eventos = $user->inscricoes()->with('evento')->get()->pluck('evento');

        return view('meus-eventos.jornada', compact('eventos'));
    }
}
