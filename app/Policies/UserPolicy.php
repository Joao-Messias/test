<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine se o usuário pode ver a lista de usuários.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine se o usuário pode ver um usuário específico.
     */
    public function view(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine se o usuário pode criar usuários.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine se o usuário pode atualizar um usuário específico.
     */
    public function update(User $user, User $model): bool
    {
        return $user->is_admin && $user->id !== $model->id;
    }

    /**
     * Determine se o usuário pode excluir um usuário específico.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->is_admin && 
               $user->id !== $model->id && 
               !$model->tasks()->exists();
    }
} 