<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\User;

class UserPolicy
{
    /**
     * Determina se o usuário pode ver a lista de outros usuários.
     */
    public function viewAny(User $user): bool
    {
        // Apenas o usuário MASTER pode ver a lista de usuários.
        return $user->tipo_usuario === UserType::MASTER;
    }

    // ... outras regras como create, update, delete para usuários
}
