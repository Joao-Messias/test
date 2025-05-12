<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Traits\HandlesDatabaseTransactions;

class CompleteTaskAction
{
    use HandlesDatabaseTransactions;

    /**
     * Executa a ação de marcar uma tarefa como concluída
     *
     * @param Task $task
     * @return Task
     */
    public function execute(Task $task): Task
    {
        return $this->executeInTransaction(function () use ($task) {
            $task->update(['status' => 'completed']);
            return $task->fresh();
        });
    }
} 