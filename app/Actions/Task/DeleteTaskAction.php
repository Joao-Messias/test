<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Traits\HandlesDatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteTaskAction
{
    use HandlesDatabaseTransactions;

    /**
     * Executa a ação de excluir uma tarefa
     *
     * @param Task $task
     * @return bool
     */
    public function execute(Task $task): bool
    {
        return $this->executeInTransaction(function () use ($task) {
            if ($task->assignedUsers()->exists()) {
                $task->assignedUsers()->detach();
            }

            return $task->delete();
        });
    }
} 