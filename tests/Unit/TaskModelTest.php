<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create([
            'owner_id' => $this->user->id
        ]);
    }

    /** @test */
    public function task_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->task->user);
        $this->assertEquals($this->user->id, $this->task->user->id);
    }

    /** @test */
    public function task_can_have_multiple_assigned_users()
    {
        $users = User::factory()->count(3)->create();
        
        $this->task->assignedUsers()->attach($users->pluck('id'));

        $this->assertCount(3, $this->task->assignedUsers);
        $this->assertTrue($this->task->assignedUsers->contains($users->first()));
    }

    /** @test */
    public function task_can_remove_assigned_users()
    {
        $user = User::factory()->create();
        $this->task->assignedUsers()->attach($user->id);

        $this->task->assignedUsers()->detach($user->id);

        $this->assertCount(0, $this->task->fresh()->assignedUsers);
    }

    /** @test */
    public function task_has_valid_status()
    {
        $this->assertContains($this->task->status, ['pending', 'completed']);
    }

    /** @test */
    public function task_requires_title()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Task::create([
            'description' => 'Description without title',
            'status' => 'pending',
            'owner_id' => $this->user->id
        ]);
    }

    /** @test */
    public function task_requires_status()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Task::create([
            'title' => 'Task without status',
            'description' => 'Description',
            'owner_id' => $this->user->id,
            'status' => null
        ]);
    }

    /** @test */
    public function task_requires_owner()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Task::create([
            'title' => 'Task without owner',
            'description' => 'Description',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function task_can_have_null_description()
    {
        $task = Task::create([
            'title' => 'Task without description',
            'status' => 'pending',
            'owner_id' => $this->user->id
        ]);

        $this->assertNull($task->description);
    }

    /** @test */
    public function task_can_be_marked_as_completed()
    {
        $this->task->update(['status' => 'completed']);

        $this->assertEquals('completed', $this->task->fresh()->status);
    }

    /** @test */
    public function task_can_be_marked_as_pending()
    {
        $this->task->update(['status' => 'completed']);
        $this->task->update(['status' => 'pending']);

        $this->assertEquals('pending', $this->task->fresh()->status);
    }
} 