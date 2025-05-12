<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Models\User;
use App\Traits\HandlesDatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RemoveUserFromTaskAction
{
    use HandlesDatabaseTransactions;

    /**
     * Executa a ação de remover um usuário da tarefa
     *
     * @param Task $task
     * @param int $userId
     * @return void
     */
    public function execute(Task $task, int $userId): void
    {
        $this->executeInTransaction(function () use ($task, $userId) {
            $user = User::findOrFail($userId);
            $task->assignedUsers()->detach($userId);
        });
    }
} 