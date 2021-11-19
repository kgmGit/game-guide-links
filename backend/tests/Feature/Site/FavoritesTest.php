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

    public function test正常系_お気に入りサイト複数(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);
        $deleteUser = User::factory()->create(['name' => 'deleteUser']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->saveMany([
            $favoriteSite1 = Site::factory()->for($otherUser)->make([
                'title' => 'otherUserSiteTitle',
                'url' => 'otherUserSiteUrl',
                'description' => 'otherUserSiteDescription',
            ]),
            Site::factory()->for($otherUser)->make([
                'title' => 'notFavoriteSiteTitle',
                'url' => 'notFavoriteSiteUrl',
                'description' => 'notFavoriteDescription',
            ]),
            $favoriteSite2 = Site::factory()->for($deleteUser)->make([
                'title' => 'deleteUserSiteTitle',
                'url' => 'deleteUserSiteUrl',
                'description' => 'deleteUserSiteDescription',
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

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'otherUserSiteTitle',
                    'url' => 'otherUserSiteUrl',
                    'description' => 'otherUserSiteDescription',
                    'favorites_count' => 1,
                    'favorited' => true,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => false,
                    'owner_name' => 'otherUser',
                    'game_title' => 'game_title',
                ],
                [
                    'id' => 3,
                    'title' => 'deleteUserSiteTitle',
                    'url' => 'deleteUserSiteUrl',
                    'description' => 'deleteUserSiteDescription',
                    'favorites_count' => 1,
                    'favorited' => true,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => false,
                    'owner_name' => null,
                    'game_title' => 'game_title',
                ],
            ]
        ]);
    }

    public function test正常系_いいねあり(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->save(
            $site = Site::factory()->for($user)->make([
                'title' => 'title',
                'url' => 'url',
                'description' => 'description',
            ])
        );

        $user->favorites()->save($site->favorites()->make());
        $user->likes()->save($site->likes()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/favorites/sites');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'title',
                    'url' => 'url',
                    'description' => 'description',
                    'favorites_count' => 1,
                    'favorited' => true,
                    'likes_count' => 1,
                    'liked' => true,
                    'owner' => true,
                    'owner_name' => 'name',
                    'game_title' => 'game_title',
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
                'url' => 'url',
                'description' => 'description',
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
                'url' => 'url',
                'description' => 'description',
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
                'url' => 'url',
                'description' => 'description',
            ])
        );

        $this->actingAs($user);
        $user->favorites()->save($site->favorites()->make());

        $response = $this->json('GET', 'api/favorites/sites');
        $response->assertStatus(403);
    }
}
