<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    #[Test]
    public function it_can_list_users()
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(6, 'data') // 5 + 1 (authenticated user)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
                ],
                'meta'
            ]);
    }

    #[Test]
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => $userData['name'],
                'email' => $userData['email']
            ]);
    }

    #[Test]
    public function it_can_show_a_user()
    {
        $response = $this->getJson("/api/users/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(
                [
                    'name' => $this->user->name,
                    'email' => $this->user->email
                ]
            );
    }

    #[Test]
    public function it_can_update_a_user()
    {
        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->patchJson("/api/users/{$this->user->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment($updatedData);

        $this->assertDatabaseHas('users', $updatedData);
    }

    #[Test]
    public function it_can_destroy_a_user()
    {

        $response = $this->deleteJson("/api/users/{$this->user->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $this->user->id]);
    }
}
