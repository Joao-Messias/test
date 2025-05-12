<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskFilterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'status' => true,
            'is_admin' => false
        ]);
        $this->otherUser = User::factory()->create([
            'status' => true,
            'is_admin' => false
        ]);

        // Create some tasks for testing
        Task::factory()->create([
            'owner_id' => $this->user->id,
            'title' => 'Pending Task 1',
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'owner_id' => $this->user->id,
            'title' => 'Completed Task 1',
            'status' => 'completed'
        ]);

        Task::factory()->create([
            'owner_id' => $this->otherUser->id,
            'title' => 'Pending Task 2',
            'status' => 'pending'
        ]);

        Task::factory()->create([
            'owner_id' => $this->otherUser->id,
            'title' => 'Completed Task 2',
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function can_filter_tasks_by_title()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('tasks.index', ['title' => 'Pending']));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 2 && 
                   $tasks->every(fn($task) => str_contains($task->title, 'Pending'));
        });
    }

    /** @test */
    public function can_filter_tasks_by_status()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('tasks.index', ['status' => 'completed']));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 2 && 
                   $tasks->every(fn($task) => $task->status === 'completed');
        });
    }

    /** @test */
    public function can_filter_tasks_by_owner()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('tasks.index', ['user_id' => $this->otherUser->id]));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 2 && 
                   $tasks->every(fn($task) => $task->owner_id === $this->otherUser->id);
        });
    }

    /** @test */
    public function can_filter_tasks_by_assigned_user()
    {
        $this->actingAs($this->user);

        // Create a task and assign it to the other user
        $task = Task::factory()->create([
            'owner_id' => $this->user->id
        ]);
        $task->assignedUsers()->attach($this->otherUser->id);

        $response = $this->get(route('tasks.index', ['assigned_user_id' => $this->otherUser->id]));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) use ($task) {
            return $tasks->contains($task);
        });
    }

    /** @test */
    public function can_combine_multiple_filters()
    {
        $this->actingAs($this->user);

        // Create a specific task for testing
        $task = Task::factory()->create([
            'owner_id' => $this->user->id,
            'title' => 'Specific Task',
            'status' => 'pending'
        ]);
        $task->assignedUsers()->attach($this->otherUser->id);

        $response = $this->get(route('tasks.index', [
            'title' => 'Specific',
            'status' => 'pending',
            'user_id' => $this->user->id,
            'assigned_user_id' => $this->otherUser->id
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) use ($task) {
            return $tasks->count() === 1 && 
                   $tasks->first()->id === $task->id;
        });
    }

    /** @test */
    public function displays_all_tasks_when_no_filter_is_applied()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            // Count only the tasks created in setUp (4 tasks)
            return $tasks->count() === 4;
        });
    }
} 