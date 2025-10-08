<?php

namespace App\Providers;

use App\Models\Event; // Adicionada p/ eventos
use App\Policies\EventPolicy; // Adicionada p/ eventos
use App\Models\User; // Adicionada p/ usuarios
use App\Policies\UserPolicy; // Adicionada p/ usuarios
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Event::class => EventPolicy::class, // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class, 
    ];

    public function boot(): void
    {
        // $this->registerPolicies(); // se usar policies
        
    }
}
