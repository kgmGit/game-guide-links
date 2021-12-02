<?php

namespace Tests\Feature\Site;

use App\Models\Game;
use App\Models\Site;
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
        $deleteUser = User::factory()->create(['name' => 'deleteUser']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->saveMany([
            $favoriteSite1 = Site::factory()->for($otherUser)->make([
                'title' => 'otherUserSiteTitle',
            ]),
            Site::factory()->for($otherUser)->make([
                'title' => 'notFavoriteSiteTitle',
            ]),
            $favoriteSite2 = Site::factory()->for($deleteUser)->make([
                'title' => 'deleteUserSiteTitle',
            ]),
        ]);
        $loginUser->favorites()->saveMany([
            $favoriteSite1->favorites()->make(),
            $favoriteSite2->favorites()->make(),
        ]);

        $deleteUser->delete();

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/favorites/sites');
        $response->assertStatus(200);

        $response
            ->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    [
                        'id' => 1,
                        'title' => 'otherUserSiteTitle',
                        'favorites_count' => 1,
                        'favorited' => true,
                    ],
                    [
                        'id' => 3,
                        'title' => 'deleteUserSiteTitle',
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

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->save(
            $site = Site::factory()->for($user)->make([
                'title' => 'title',
            ])
        );

        $this->actingAs($user);
        $response = $this->json('GET', 'api/favorites/sites');
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
        $game->sites()->save(
            $site = Site::factory()->for($user)->make([
                'title' => 'title',
            ])
        );

        $user->favorites()->save($site->favorites()->make());

        $response = $this->json('GET', 'api/favorites/sites');
        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->save(
            $site = Site::factory()->for($user)->make([
                'title' => 'title',
            ])
        );

        $this->actingAs($user);
        $user->favorites()->save($site->favorites()->make());

        $response = $this->json('GET', 'api/favorites/sites');
        $response->assertStatus(403);
    }
}
