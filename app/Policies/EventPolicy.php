<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Enums\UserType;

class EventPolicy
{
    /**
     * Determina se o usuário pode ver qualquer evento (a lista de eventos).
     */
    public function viewAny(User $user): bool
    {
        // Permite que admins e masters vejam a lista no painel
        return in_array($user->tipo_usuario, [
            UserType::ADMIN,
            UserType::MASTER,
        ]);
    }

    /**
     * Determina se o usuário pode ver um evento específico.
     */
    public function view(User $user, Event $event): bool
    {
        // Por enquanto, a regra é a mesma que para ver a lista
        return $this->viewAny($user);
    }

    /**
     * Determina se o usuário pode criar eventos.
     */
    public function create(User $user): bool
    {
        // Permite a ação se o tipo do usuário for ADMIN ou MASTER
        return in_array($user->tipo_usuario, [
            UserType::ADMIN,
            UserType::MASTER,
        ]);
    }

    /**
     * Determina se o usuário pode atualizar (editar) um evento.
     */
    public function update(User $user, Event $event): bool
    {
        // 1. O usuário MASTER pode editar a qualquer momento.
        if ($user->tipo_usuario === UserType::MASTER) {
            return true;
        }

        // 2. O usuário ADMIN só pode editar se o evento ainda não começou.
        if ($user->tipo_usuario === UserType::ADMIN) {
            // A data de início do evento é no futuro?
            // O Laravel/Carbon torna essa verificação muito simples.
            return $event->data_inicio_evento->isFuture();
        }

        // 3. Outros tipos de usuário não podem editar.
        return false;
    }

    /**
     * Determina se o usuário pode deletar um evento.
     */
    public function delete(User $user, Event $event): bool
    {
        // Apenas Masters podem deletar eventos
        return $user->tipo_usuario === UserType::MASTER;
    }
}