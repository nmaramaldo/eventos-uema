<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paginação com Bootstrap (compat 4/5)
        if (method_exists(Paginator::class, 'useBootstrapFive')) {
            Paginator::useBootstrapFive();
        } else {
            Paginator::useBootstrap();
        }

        // Versão/Release no footer
        try {
            // Tenta tag mais recente; se não houver, usa hash curto
            $gitVersion = @trim(shell_exec('git describe --tags --always 2>/dev/null'));
            if (!$gitVersion) {
                $gitVersion = @trim(shell_exec('git rev-parse --short HEAD 2>/dev/null'));
            }
            if (!$gitVersion) {
                // fallback para config/env, se existir
                $gitVersion = config('app.version') ?? env('APP_VERSION', 'N/A');
            }
            if (!$gitVersion) {
                $gitVersion = 'N/A';
            }
            view()->share('gitVersion', $gitVersion);
        } catch (\Throwable $e) {
            view()->share('gitVersion', 'N/A');
        }

        // Apenas admin/master ATIVOS podem gerenciar usuários
        Gate::define('manage-users', function ($user) {
            $tipo = $user->tipo_usuario;

            if ($tipo instanceof \BackedEnum) {
                $tipo = $tipo->value; // 'admin' | 'master'
            } elseif ($tipo instanceof \UnitEnum) {
                $tipo = $tipo->name;
            }

            return $user->ativo && in_array((string) $tipo, ['admin','master'], true);
        });
    }
}