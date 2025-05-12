<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    /** @test */
    public function user_can_create_task()
    {
        $this->actingAs($this->user);

        $taskData = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending'
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'owner_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_edit_own_task()
    {
        $this->actingAs($this->user);
        $task = Task::factory()->create(['owner_id' => $this->user->id]);

        $updatedData = [
            'title' => 'Updated Task',
            'description' => 'Updated Description',
            'status' => 'completed'
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updatedData['title'],
            'description' => $updatedData['description'],
            'status' => $updatedData['status']
        ]);
    }

    /** @test */
    public function user_can_delete_own_task()
    {
        $this->actingAs($this->user);
        $task = Task::factory()->create(['owner_id' => $this->user->id]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_can_mark_task_as_completed()
    {
        $this->actingAs($this->user);
        $task = Task::factory()->create([
            'owner_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->patch(route('tasks.complete', $task));

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function validates_required_fields_when_creating_task()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('tasks.store'), []);

        $response->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function validates_required_fields_when_updating_task()
    {
        $this->actingAs($this->user);
        $task = Task::factory()->create(['owner_id' => $this->user->id]);

        $response = $this->put(route('tasks.update', $task), []);

        $response->assertSessionHasErrors(['title']);
    }
} 