<?php

namespace App\Actions\Task;

use App\Models\Task;
use App\Traits\HandlesDatabaseTransactions;

class CreateTaskAction
{
    use HandlesDatabaseTransactions;

    /**
     * Executa a ação de criar uma nova tarefa
     *
     * @param array $data
     * @param int $ownerId
     * @return Task
     */
    public function execute(array $data, int $ownerId): Task
    {
        return $this->executeInTransaction(function () use ($data, $ownerId) {
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'owner_id' => $ownerId
            ]);

            if (isset($data['users'])) {
                $task->assignedUsers()->attach($data['users']);
            }

            return $task;
        });
    }
} 