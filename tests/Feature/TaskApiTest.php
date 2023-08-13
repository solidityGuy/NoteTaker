<?php

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class TaskApiTest extends TestCase
{

    protected $authenticatedUser;

    // Authenticate
    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticatedUser = User::factory()->create();
        Sanctum::actingAs($this->authenticatedUser);
    }

    // Test creating a task
    public function testCreateTask()
    {
        $task = Task::factory()->create();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    // Test fetching the tasks
    public function testGetTasks()
    {
        $response = $this->get('/api/tasks');
        $response->assertStatus(200);
    }

    // Test fetching a task
    public function testGetTask()
    {
        $task = Task::factory()->create();
        $response = $this->get("/api/tasks/{$task->id}");
        $response->assertStatus(200);
    }

    // Test updating a task
    public function testUpdateTask()
    {
        $task = Task::factory()->create();

        $data = [
            'title' => 'Updated task title',
            'description' => 'Updated description',
            'completed' => true
        ];

        $response = $this->postJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => "Updated task with id: {$task->id}"]);
        $this->assertDatabaseHas('tasks', $data);
    }

    // Test deleting a task
    public function testDeleteTask()
    {
        $task = Task::factory()->create();

        $response = $this->delete("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing($task, ['id' => $task->id]);
    }
}
