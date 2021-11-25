<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_投稿ゲーム複数(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);

        $game1 = Game::factory()->for($loginUser)->create(['title' => 'game_title1']);
        $game2 = Game::factory()->for($loginUser)->create(['title' => 'game_title2']);
        $game3 = Game::factory()->for($otherUser)->create(['title' => 'game_title3']);

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/posts/games');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'game_title1',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => true,
                ],
                [
                    'id' => 2,
                    'title' => 'game_title2',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'owner' => true,
                ],
            ]
        ]);
    }

    public function test正常系_お気に入りあり(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);
        $game = Game::factory()->for($user)->create(['title' => 'game_title']);
        $game->favoriteUsers()->attach($user->id);

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/games');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'game_title',
                    'favorites_count' => 1,
                    'favorited' => true,
                    'owner' => true,
                ],
            ]
        ]);
    }

    public function test正常系_投稿ゲームなし(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);
        Game::factory()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/games');
        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [

            ]
        ]);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);
        $game = Game::factory()->for($user)->create(['title' => 'game_title']);

        $response = $this->json('GET', 'api/posts/games');

        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create(['name' => 'name']);
        $game = Game::factory()->for($user)->create(['title' => 'game_title']);
        $game->favoriteUsers()->attach($user->id);

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/games');

        $response->assertStatus(403);
    }
}
