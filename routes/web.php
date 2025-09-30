<?php

use Illuminate\Support\Facades\Route;

// Front (público)
use App\Http\Controllers\FrontController;

// Protegidos / app
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventoDetalheController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\PalestranteController;
use App\Http\Controllers\NotificacaoController;

// Admin
use App\Http\Controllers\Admin\UserAdminController;

/*
|--------------------------------------------------------------------------
| PÚBLICO
|--------------------------------------------------------------------------
*/

// Home pública
Route::get('/', [FrontController::class, 'home'])->name('front.home');

// Listagem e detalhe públicos de eventos
Route::prefix('eventos')->name('front.eventos.')->group(function () {
    // OBS: o método correto na FrontController é "index"
    Route::get('/', [FrontController::class, 'index'])->name('index');
    // {evento} usa implicit binding (UUID)
    Route::get('/{evento}', [FrontController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD / PERFIL (PROTEGIDO)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| CRUDS DA APLICAÇÃO (PROTEGIDOS)
|--------------------------------------------------------------------------
| Prefixo /app para não colidir com as rotas públicas.
| Os NOMES permanecem iguais (eventos.index, palestrantes.index, ...).
*/

Route::middleware('auth')->prefix('app')->group(function () {
    // Atalho: /app -> /app/eventos
    Route::get('/', fn () => redirect()->route('eventos.index'))->name('app.home');

    // Eventos e detalhes
    Route::resource('eventos', EventController::class);
    Route::resource('eventos_detalhes', EventoDetalheController::class);

    // Programação POR EVENTO (atalhos amigáveis)
    Route::get('eventos/{evento}/programacao',        [EventoDetalheController::class, 'indexByEvent'])->name('eventos.programacao.index');
    Route::get('eventos/{evento}/programacao/create', [EventoDetalheController::class, 'createForEvent'])->name('eventos.programacao.create');
    Route::post('eventos/{evento}/programacao',       [EventoDetalheController::class, 'storeForEvent'])->name('eventos.programacao.store');

    // Inscrições, certificados e palestrantes
    Route::resource('inscricoes',   InscricaoController::class);
    Route::resource('certificados', CertificadoController::class);
    Route::resource('palestrantes', PalestranteController::class);

    // Notificações
    Route::resource('notificacoes', NotificacaoController::class);
    Route::patch(
        'notificacoes/{notificacao}/marcar-como-lida',
        [NotificacaoController::class, 'marcarComoLida']
    )->name('notificacoes.marcarComoLida');
});

/*
|--------------------------------------------------------------------------
| ADMIN (PROTEGIDO + PERMISSÃO)
|--------------------------------------------------------------------------
| Acesso permitido apenas para usuários com tipo 'admin' ou 'master'.
| A gate 'manage-users' é definida no AppServiceProvider.
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
| Auth scaffolding (login, register, etc.)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
