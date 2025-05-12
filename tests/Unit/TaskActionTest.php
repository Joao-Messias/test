<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Actions\Task\CreateTaskAction;
use App\Actions\Task\UpdateTaskAction;
use App\Actions\Task\DeleteTaskAction;
use App\Actions\Task\AddUserToTaskAction;
use App\Actions\Task\RemoveUserFromTaskAction;
use App\Actions\Task\CompleteTaskAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskActionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;
    private Task $task;
    private CreateTaskAction $createAction;
    private UpdateTaskAction $updateAction;
    private DeleteTaskAction $deleteAction;
    private AddUserToTaskAction $addUserAction;
    private RemoveUserFromTaskAction $removeUserAction;
    private CompleteTaskAction $completeAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->task = Task::factory()->create([
            'owner_id' => $this->user->id
        ]);

        $this->createAction = app(CreateTaskAction::class);
        $this->updateAction = app(UpdateTaskAction::class);
        $this->deleteAction = app(DeleteTaskAction::class);
        $this->addUserAction = app(AddUserToTaskAction::class);
        $this->removeUserAction = app(RemoveUserFromTaskAction::class);
        $this->completeAction = app(CompleteTaskAction::class);
    }

    /** @test */
    public function create_task_action_cria_tarefa_com_sucesso()
    {
        $data = [
            'title' => 'Nova Tarefa',
            'description' => 'Descrição da tarefa',
            'status' => 'pending',
            'users' => [$this->otherUser->id]
        ];

        $task = $this->createAction->execute($data, $this->user->id);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($data['title'], $task->title);
        $this->assertEquals($data['description'], $task->description);
        $this->assertEquals($data['status'], $task->status);
        $this->assertEquals($this->user->id, $task->owner_id);
        $this->assertTrue($task->assignedUsers->contains($this->otherUser));
    }

    /** @test */
    public function update_task_action_atualiza_tarefa_com_sucesso()
    {
        $data = [
            'title' => 'Tarefa Atualizada',
            'description' => 'Nova descrição',
            'status' => 'completed',
            'users' => [$this->otherUser->id]
        ];

        $this->updateAction->execute($this->task, $data);

        $this->task->refresh();
        $this->assertEquals($data['title'], $this->task->title);
        $this->assertEquals($data['description'], $this->task->description);
        $this->assertEquals($data['status'], $this->task->status);
        $this->assertTrue($this->task->assignedUsers->contains($this->otherUser));
    }

    /** @test */
    public function delete_task_action_exclui_tarefa_com_sucesso()
    {
        $taskId = $this->task->id;
        $this->deleteAction->execute($this->task);

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    /** @test */
    public function add_user_action_adiciona_usuario_a_tarefa()
    {
        $this->addUserAction->execute($this->task, $this->otherUser->id);

        $this->assertTrue($this->task->fresh()->assignedUsers->contains($this->otherUser));
    }

    /** @test */
    public function remove_user_action_remove_usuario_da_tarefa()
    {
        $this->task->assignedUsers()->attach($this->otherUser->id);
        $this->removeUserAction->execute($this->task, $this->otherUser->id);

        $this->assertFalse($this->task->fresh()->assignedUsers->contains($this->otherUser));
    }

    /** @test */
    public function complete_action_marca_tarefa_como_concluida()
    {
        $this->completeAction->execute($this->task);

        $this->assertEquals('completed', $this->task->fresh()->status);
    }
} 