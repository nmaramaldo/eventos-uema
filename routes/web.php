<?php

use Illuminate\Support\Facades\Route;

// Front (público)
use App\Http\Controllers\FrontController;

// Protegidos / app
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\PalestranteController;
use App\Http\Controllers\NotificacaoController;

// Admin
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProgramacaoController;

/*
|--------------------------------------------------------------------------
| PÚBLICO
|--------------------------------------------------------------------------
*/

// Home pública
Route::get('/', [FrontController::class, 'home'])->name('front.home');

// Listagem e detalhe públicos de eventos
Route::prefix('eventos')->name('front.eventos.')->group(function () {
    Route::get('/', [FrontController::class, 'index'])->name('index');
    Route::get('/{evento}', [FrontController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO E PERFIL
|--------------------------------------------------------------------------
*/

// Auth scaffolding (login, register, logout, etc.)
require __DIR__ . '/auth.php';

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| CRUDS DA APLICAÇÃO (PROTEGIDOS)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('app')->group(function () {
    Route::get('/', fn() => redirect()->route('eventos.index'))->name('app.home');

    // ---- Administrativo (admin/master) ----
    Route::middleware('can:manage-users')->group(function () {

        // Mantém o resource mas sem create/store (usaremos o fluxo em 3 passos)
        Route::resource('eventos', EventController::class)->except(['create', 'store']);

        // Fluxo de criação em 3 passos
        Route::prefix('eventos/criar')->name('eventos.create.')->group(function () {
            // Passo 1
            Route::get('/passo-1', [EventController::class, 'createStep1'])->name('step1');
            Route::post('/passo-1', [EventController::class, 'storeStep1'])->name('store.step1');

            // Passo 2
            Route::get('/passo-2', [EventController::class, 'createStep2'])->name('step2');
            Route::post('/passo-2', [EventController::class, 'storeStep2'])->name('store.step2');

            // Passo 3
            Route::get('/passo-3', [EventController::class, 'createStep3'])->name('step3');
            Route::post('/passo-3', [EventController::class, 'storeStep3'])->name('store.step3');
        });

        // Programação (geral e por evento)
        Route::get('eventos/{evento}/programacao', [ProgramacaoController::class, 'indexByEvent'])->name('eventos.programacao.index');
        Route::get('eventos/{evento}/programacao/create', [ProgramacaoController::class, 'createForEvent'])->name('eventos.programacao.create');
        Route::post('eventos/{evento}/programacao', [ProgramacaoController::class, 'storeForEvent'])->name('eventos.programacao.store');
        Route::get('eventos/{evento}/programacao/{atividade}/edit', [ProgramacaoController::class, 'editByEvent'])->name('eventos.programacao.edit');
        Route::put('eventos/{evento}/programacao/{atividade}', [ProgramacaoController::class, 'updateByEvent'])->name('eventos.programacao.update');
        Route::delete('eventos/{evento}/programacao/{atividade}', [ProgramacaoController::class, 'destroyByEvent'])->name('eventos.programacao.destroy');


        // Palestrantes (visão por evento) + recursos
        Route::prefix('eventos/{evento}/palestrantes')->group(function () {
            Route::get('/', [PalestranteController::class, 'indexByEvent'])->name('eventos.palestrantes.index');
            Route::get('/create', [PalestranteController::class, 'createByEvent'])->name('eventos.palestrantes.create');
            Route::post('/', [PalestranteController::class, 'storeByEvent'])->name('eventos.palestrantes.store');
            Route::get('/{palestrante}/edit', [PalestranteController::class, 'editByEvent'])->name('eventos.palestrantes.edit');
            Route::put('/{palestrante}', [PalestranteController::class, 'updateByEvent'])->name('eventos.palestrantes.update');
            Route::delete('/{palestrante}', [PalestranteController::class, 'destroyByEvent'])->name('eventos.palestrantes.destroy');
        });

        Route::get('/palestrantes', [PalestranteController::class, 'index'])->name('palestrantes.index');

        // Certificados
        Route::resource('certificados', CertificadoController::class);
    });

    // ---- Área do participante/logado ----
    Route::resource('inscricoes',   InscricaoController::class);
    Route::resource('notificacoes', NotificacaoController::class);
    Route::patch(
        'notificacoes/{notificacao}/marcar-como-lida',
        [NotificacaoController::class, 'marcarComoLida']
    )->name('notificacoes.marcarComoLida');

    // Logs de auditoria
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::get('/audit-logs/model/{modelType}/{modelId?}', [AuditLogController::class, 'forModel'])->name('audit-logs.model');
});

/*
|--------------------------------------------------------------------------
| ADMIN (PROTEGIDO + PERMISSÃO)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:manage-users'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('usuarios', UserAdminController::class)->except(['show', 'destroy']);
        Route::patch('usuarios/{user}/ativar',    [UserAdminController::class, 'ativar'])->name('usuarios.ativar');
        Route::patch('usuarios/{user}/desativar', [UserAdminController::class, 'desativar'])->name('usuarios.desativar');
        Route::patch('usuarios/{user}/tipo',      [UserAdminController::class, 'alterarTipo'])->name('usuarios.tipo');
    });
