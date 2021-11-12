<?php

namespace Tests\Feature\Site;

use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create(['name' => 'name']);
        $game = $user->games()->create(['title' => 'game_title']);
        Site::factory()->for($user)->for($game)->create([
            'title' => 'title',
            'url' => 'url',
            'description' => 'description',
        ]);

        $response = $this->json('GET', 'api/games/game_title/sites/1');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'title',
                'url' => 'url',
                'description' => 'description',
                'favorites_count' => 0,
                'favorited' => false,
                'likes_count' => 0,
                'liked' => false,
                'owner' => false,
                'owner_name' => 'name',
            ]
        ]);
    }

    public function test異常系_ゲームタイトルが存在しない(): void
    {
        $user = User::factory()->create(['name' => 'name']);
        $game = $user->games()->create(['title' => 'game_title']);
        Site::factory()->for($user)->for($game)->create([
            'title' => 'title',
            'url' => 'url',
            'description' => 'description',
        ]);

        $response = $this->json('GET', 'api/games/wrong_title/sites/1');
        $response->assertStatus(404);
    }

    public function test異常系_記事IDが存在しない(): void
    {
        $user = User::factory()->create(['name' => 'name']);
        $game = $user->games()->create(['title' => 'game_title']);
        Site::factory()->for($user)->for($game)->create([
            'title' => 'title',
            'url' => 'url',
            'description' => 'description',
        ]);

        $response = $this->json('GET', 'api/games/game_title/sites/99');
        $response->assertStatus(404);
    }
}
