<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskUserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $otherUser;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->task = Task::factory()->create([
            'owner_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_add_another_user_to_task()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('tasks.users.add', $this->task), [
            'user_id' => $this->otherUser->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('task_user', [
            'task_id' => $this->task->id,
            'user_id' => $this->otherUser->id
        ]);
    }

    /** @test */
    public function user_can_remove_user_from_task()
    {
        $this->actingAs($this->user);
        $this->task->assignedUsers()->attach($this->otherUser->id);

        $response = $this->delete(route('tasks.users.remove', $this->task), [
            'user_id' => $this->otherUser->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('task_user', [
            'task_id' => $this->task->id,
            'user_id' => $this->otherUser->id
        ]);
    }

    /** @test */
    public function cannot_add_nonexistent_user_to_task()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('tasks.users.add', $this->task), [
            'user_id' => 99999
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    /** @test */
    public function cannot_remove_user_not_assigned_to_task()
    {
        $this->actingAs($this->user);

        $response = $this->delete(route('tasks.users.remove', $this->task), [
            'user_id' => $this->otherUser->id
        ]);

        $response->assertSessionHasErrors('user_id');
    }

    /** @test */
    public function can_add_multiple_users_when_creating_task()
    {
        $this->actingAs($this->user);
        $thirdUser = User::factory()->create();

        $taskData = [
            'title' => 'Task with Multiple Users',
            'description' => 'Test Description',
            'status' => 'pending',
            'users' => [$this->otherUser->id, $thirdUser->id]
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $task = Task::where('title', $taskData['title'])->first();
        
        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'user_id' => $this->otherUser->id
        ]);
        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'user_id' => $thirdUser->id
        ]);
    }

    /** @test */
    public function can_update_task_assigned_users()
    {
        $this->actingAs($this->user);
        $thirdUser = User::factory()->create();
        $this->task->assignedUsers()->attach($this->otherUser->id);

        $updatedData = [
            'title' => $this->task->title,
            'description' => $this->task->description,
            'status' => $this->task->status,
            'users' => [$thirdUser->id]
        ];

        $response = $this->put(route('tasks.update', $this->task), $updatedData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('task_user', [
            'task_id' => $this->task->id,
            'user_id' => $this->otherUser->id
        ]);
        $this->assertDatabaseHas('task_user', [
            'task_id' => $this->task->id,
            'user_id' => $thirdUser->id
        ]);
    }
} 