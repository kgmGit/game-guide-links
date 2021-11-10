<?php

namespace Tests\Feature\User;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_ゲーム所有なし(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'user');

        $response->assertStatus(204);

        $this->assertGuest('web');

        $this->assertNull(User::first());
    }

    public function test正常系_ゲーム所有有り(): void
    {
        /** @var User $deleteUser */
        $deleteUser = User::factory()->create(['name' => 'deleteUser']);
        $deleteUser->games()->save(Game::factory()->make(['title' => 'deleteUserGame']));
        $otherUser = User::factory()->create();
        $otherUser->games()->save(Game::factory()->make());

        $this->actingAs($deleteUser);
        $response = $this->json('DELETE', 'user');

        $response->assertStatus(204);

        $this->assertGuest('web');

        $this->assertNull(User::where('name', 'deleteUser')->first());

        $deleteUserGame = Game::where('title', 'deleteUserGame')->first();
        $this->assertNotNull($deleteUserGame);
        $this->assertNull($deleteUserGame->user_id);
        $otherUserGame = $otherUser->games()->first();
        $this->assertNotNull($otherUserGame);
    }
}
