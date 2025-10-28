<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Inscricao;
use App\Policies\EventPolicy;
use App\Policies\InscricaoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Event::class => EventPolicy::class,
        Inscricao::class => InscricaoPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        
    }
}
