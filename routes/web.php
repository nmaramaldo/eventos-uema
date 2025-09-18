<?php

use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventoDetalheController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PalestranteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


// Pagina pública 

Route::get('/', function () {
    return view('welcome');
});

// Dashboard protegido

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas de perfil protegidas

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 
Route::middleware('auth')->group(function () {

    // Recursos de eventos 
    Route::resource('eventos', EventController::class);
    Route::resource('eventos_detalhes', EventoDetalheController::class);

    // Recursos de inscrições, certificados e palestrantes
    Route::resource('inscricoes',InscricaoController::class);
    Route::resource('certificados', CertificadoController::class);
    Route::resource('palestrantes', PalestranteController::class);

    // Recursos de notificações
    Route::resource('notificacoes', NotificacaoController::class);

    // Rota customizada para marcar como lida
    Route::patch('notificacoes/{notificacao}/marcar-como-lida', [NotificacaoController::class, 'marcarComoLida'])->name('notificacoes.marcarComoLida');

});

require __DIR__ . '/auth.php';
