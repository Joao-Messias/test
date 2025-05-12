<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Models\User;
use App\Traits\HandlesDatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AddUserToTaskAction
{
    use HandlesDatabaseTransactions;

    /**
     * Executa a ação de adicionar um usuário à tarefa
     *
     * @param Task $task
     * @param int $userId
     * @return void
     */
    public function execute(Task $task, int $userId): void
    {
        $this->executeInTransaction(function () use ($task, $userId) {
            $user = User::findOrFail($userId);
            
            if (!$task->assignedUsers()->where('user_id', $userId)->exists()) {
                $task->assignedUsers()->attach($userId);
            }
        });
    }
} 