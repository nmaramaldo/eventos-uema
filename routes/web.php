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
use App\Http\Controllers\RelatorioController;

// Admin
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProgramacaoController;

// ✅ novo controller (Meus eventos)
use App\Http\Controllers\MyEventsController;

/*
|--------------------------------------------------------------------------
| PÚBLICO
|--------------------------------------------------------------------------
*/

Route::get('/', [FrontController::class, 'home'])->name('front.home');

Route::prefix('eventos')->name('front.eventos.')->group(function () {
    Route::get('/', [FrontController::class, 'index'])->name('index');
    Route::get('/{evento}', [FrontController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| AUTENTICAÇÃO E PERFIL
|--------------------------------------------------------------------------
*/

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

    // “Meus eventos”
    Route::get('/meus-eventos', [MyEventsController::class, 'index'])->name('meus-eventos.index');
    Route::get('/meus-eventos/{evento}/editar', [MyEventsController::class, 'edit'])->name('meus-eventos.edit');

    // ---- Administrativo (admin/master) ----
    Route::middleware('can:manage-users')->group(function () {
        // Eventos (admin)
        Route::resource('eventos', EventController::class);

        // Palestrantes (admin)
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

    // ---- Programação (somente usuários autenticados; filtro real no controller: Admin/Master) ----
    Route::post('eventos/{evento}/programacao/store-ajax', [ProgramacaoController::class, 'storeAjaxForEvent'])->name('eventos.programacao.store.ajax');
    Route::get('eventos/{evento}/programacao', [ProgramacaoController::class, 'indexByEvent'])->name('eventos.programacao.index');
    Route::get('eventos/{evento}/programacao/create', [ProgramacaoController::class, 'createForEvent'])->name('eventos.programacao.create');
    Route::post('eventos/{evento}/programacao', [ProgramacaoController::class, 'storeForEvent'])->name('eventos.programacao.store');
    Route::get('eventos/{evento}/programacao/{atividade}/edit', [ProgramacaoController::class, 'editByEvent'])->name('eventos.programacao.edit');
    Route::put('eventos/{evento}/programacao/{atividade}', [ProgramacaoController::class, 'updateByEvent'])->name('eventos.programacao.update');
    Route::delete('eventos/{evento}/programacao/{atividade}', [ProgramacaoController::class, 'destroyByEvent'])->name('eventos.programacao.destroy');

    // ---- Área do participante/logado ----
    Route::resource('inscricoes',   InscricaoController::class);
    Route::resource('notificacoes', NotificacaoController::class);

    Route::patch('notificacoes/{notificacao}/marcar-como-lida', [NotificacaoController::class, 'marcarComoLida'])
        ->name('notificacoes.marcarComoLida');

    Route::delete('/inscricoes/{inscricao}', [InscricaoController::class, 'destroy'])
        ->name('inscricoes.cancelar')
        ->middleware('auth');

    // Logs de auditoria
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    Route::get('/audit-logs/model/{modelType}/{modelId?}', [AuditLogController::class, 'forModel'])->name('audit-logs.model');
});

/*
|--------------------------------------------------------------------------
| ADMIN
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

/*
|--------------------------------------------------------------------------
| RELATÓRIOS (somente Master/Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');        
    Route::get('/relatorios/eventos', [RelatorioController::class, 'listaEventos'])->name('relatorios.eventos');    
    Route::get('/relatorios/eventos/{evento}', [RelatorioController::class, 'showEvento'])->name('relatorios.evento.show');
    Route::get('/relatorios/eventos/{evento}/pdf', [RelatorioController::class, 'exportarPDF'])->name('relatorios.evento.pdf');
});
