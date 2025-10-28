<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Inscricao;
use App\Policies\EventPolicy;
use App\Policies\InscricaoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Event::class => EventPolicy::class,
        Inscricao::class => InscricaoPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        /*
        |--------------------------------------------------------------------------
        | Definições de permissões (Gates)
        |--------------------------------------------------------------------------
        */

        // Permitir acesso d usuário master
        Gate::define('isAdmin', function ($user) {
            
            return in_array($user->tipo, ['master']);
        });

        
    }
}
