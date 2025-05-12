<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Traits\HandlesDatabaseTransactions;

class UpdateTaskAction
{
    use HandlesDatabaseTransactions;

    /**
     * Executa a ação de atualizar uma tarefa existente
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function execute(Task $task, array $data): Task
    {
        return $this->executeInTransaction(function () use ($task, $data) {
            $task->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? $task->status
            ]);

            if (isset($data['users'])) {
                $task->assignedUsers()->sync($data['users']);
            }

            return $task->fresh();
        });
    }
} 