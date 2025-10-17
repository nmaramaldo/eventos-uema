<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Event $event): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        $tipo = method_exists($user, 'tipoUsuarioValue') ? $user->tipoUsuarioValue() : $user->tipo_usuario;
        return in_array($tipo, ['admin', 'master'], true);
    }

    public function update(User $user, Event $event): bool
    {
        $tipo = method_exists($user, 'tipoUsuarioValue') ? $user->tipoUsuarioValue() : $user->tipo_usuario;
        if ($tipo === 'master') return true;
        if ($tipo === 'admin')  return now()->lt($event->data_inicio_evento);
        return false;
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }
}
