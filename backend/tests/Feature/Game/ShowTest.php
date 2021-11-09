<?php

namespace Tests\Feature\Game;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        $user1 = User::factory()->create();

        $user1->games()->create(['title' => 'title']);

        $response = $this->json('GET', 'api/games/title');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'title',
                'favorites_count' => 0,
                'favorited' => false,
                'owner' => false,
            ]
        ]);
    }

    public function test異常系_ゲームタイトルが存在しない(): void
    {
        $user1 = User::factory()->create();

        $user1->games()->create(['title' => 'title']);

        $response = $this->json('GET', 'api/games/wrong_title');
        $response->assertStatus(404);
    }
}
