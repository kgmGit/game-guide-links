<?php

namespace Tests\Feature\Article;

use App\Models\Article;
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
        Article::factory()->for($user)->for($game)->create([
            'title' => 'title',
            'outline' => 'outline',
            'content' => 'content',
        ]);

        $response = $this->json('GET', 'api/games/game_title/articles/1');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                'id' => 1,
                'title' => 'title',
                'outline' => 'outline',
                'content' => 'content',
                'favorites_count' => 0,
                'favorited' => false,
                'likes_count' => 0,
                'liked' => false,
                'owner' => false,
                'owner_name' => 'name',
                'game_title' => 'game_title',
                ]
        ]);
    }

    public function test異常系_ゲームタイトルが存在しない(): void
    {
        $user = User::factory()->create(['name' => 'name']);
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create([
            'title' => 'title',
            'outline' => 'outline',
            'content' => 'content',
        ]);

        $response = $this->json('GET', 'api/games/wrong_title/articles/1');
        $response->assertStatus(404);
    }

    public function test異常系_紐付いているゲームでない(): void
    {
        $user = User::factory()->create(['name' => 'name']);
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create([
            'title' => 'title',
            'outline' => 'outline',
            'content' => 'content',
        ]);

        $user->games()->create(['title' => 'not_link_game_title']);

        $response = $this->json('GET', 'api/games/not_link_game_title/articles/1');
        $response->assertStatus(404);
    }

    public function test異常系_記事IDが存在しない(): void
    {
        $user = User::factory()->create(['name' => 'name']);
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create([
            'title' => 'title',
            'outline' => 'outline',
            'content' => 'content',
        ]);

        $response = $this->json('GET', 'api/games/game_title/articles/99');
        $response->assertStatus(404);
    }
}
