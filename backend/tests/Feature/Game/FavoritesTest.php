<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);

        $game1 = Game::factory()->for($loginUser)->create(['title' => 'game_title1']);
        $game2 = Game::factory()->create(['title' => 'game_title2']);
        $game3 = Game::factory()->create(['title' => 'game_title3']);

        $game1->favoriteUsers()->attach($loginUser->id);
        $game2->favoriteUsers()->attach($loginUser->id);
        $game3->favoriteUsers()->attach($otherUser->id);


        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/favorites/games');
        $response->assertStatus(200);

        $response->assertJsonCount(2, 'data')
            ->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'game_title1',
                    'favorites_count' => 1,
                    'favorited' => true,
                ],
                [
                    'id' => 2,
                    'title' => 'game_title2',
                    'favorites_count' => 1,
                    'favorited' => true,
                ],
            ]
        ]);
    }

    public function test正常系_お気に入りなし(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);
        Game::factory()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('GET', 'api/favorites/games');
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
        $game = Game::factory()->create(['title' => 'game_title']);
        $game->favoriteUsers()->attach($user->id);

        $response = $this->json('GET', 'api/favorites/games');

        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create(['name' => 'name']);
        $game = Game::factory()->create(['title' => 'game_title']);
        $game->favoriteUsers()->attach($user->id);

        $this->actingAs($user);
        $response = $this->json('GET', 'api/favorites/games');

        $response->assertStatus(403);
    }
}
