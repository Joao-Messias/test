<?php

namespace App\Actions\User;

use App\Models\User;

class DeleteUserAction
{
    /**
     * Executa a ação de excluir um usuário
     *
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function execute(User $user): bool
    {
        if ($user->tasks()->exists() || $user->assignedTasks()->exists()) {
            throw new \Exception('Não é possível excluir um usuário que possui tarefas vinculadas ou atribuídas.');
        }

        return $user->delete();
    }
} 