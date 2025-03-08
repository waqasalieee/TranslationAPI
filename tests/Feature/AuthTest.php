<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user login with correct credentials.
     *
     * @return void
     */
    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    /**
     * Test user login with wrong credentials.
     *
     * @return void
     */
    public function test_user_cannot_login_with_wrong_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        //echo "User email = $user->email \n";
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }
}
