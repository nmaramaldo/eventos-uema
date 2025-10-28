<?php

namespace App\Policies;

use App\Models\Inscricao;
use App\Models\User;
use App\Enums\UserType; 

class InscricaoPolicy
{
    /**
     * Determina se o usuário pode ver qualquer inscrição.
     */
    public function viewAny(User $user): bool
    {
        // Apenas Admin ou Master podem ver todas as inscrições de todos
        return $user->tipo_usuario === UserType::ADMIN || $user->tipo_usuario === UserType::MASTER;
    }

    /**
     * Determina se o usuário pode ver uma inscrição específica.
     */
    public function view(User $user, Inscricao $inscricao): bool
    {
        // Permite se for Admin/Master OU se o usuário for o dono da inscrição
        if ($this->viewAny($user)) {
            return true;
        }
        return $user->id === $inscricao->user_id;
    }

    /**
     * Determina se o usuário pode criar uma nova inscrição.
     */
    public function create(User $user): bool
    {
        // Qualquer usuário logado pode tentar se inscrever
        return true;
    }

    /**
     * Determina se o usuário pode deletar (cancelar) uma inscrição.
     */
    public function delete(User $user, Inscricao $inscricao): bool
    {
        // Regra 1: Permite se o usuário for Admin ou Master.
        if ($user->tipo_usuario === UserType::ADMIN || $user->tipo_usuario === UserType::MASTER) {
            return true;
        }

        // Regra 2: Permite se o ID do usuário logado for o mesmo ID do "dono" da inscrição.
        return $user->id === $inscricao->user_id;
    }
}