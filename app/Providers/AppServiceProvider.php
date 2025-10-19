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
        // Paginação com Bootstrap (opcional)
        if (method_exists(Paginator::class, 'useBootstrapFive')) {
            Paginator::useBootstrapFive();
        } else {
            Paginator::useBootstrap();
        }

        try {
            $gitVersion = trim(shell_exec('git describe --tags --abbrev=0'));
            view()->share('gitVersion', $gitVersion);
        } catch (\Exception $e) {
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
