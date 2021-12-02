<?php

namespace Tests\Feature\Site;

use App\Models\Favorite;
use App\Models\Game;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_サイト作成者複数(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);
        $deleteUser = User::factory()->create(['name' => 'deleteUser']);

        $game = Game::factory()->create(['title' => 'aaa']);
        $game->sites()->saveMany([
            Site::factory()->for($loginUser)->make([
                'title' => 'loginUserSiteTitle',
                'url' => 'loginUserSiteUrl',
                'description' => 'loginUserSiteDescription',
            ]),
            Site::factory()->for($otherUser)->make([
                'title' => 'otherUserSiteTitle',
                'url' => 'otherUserSiteUrl',
                'description' => 'otherUserSiteDescription',
            ]),
            Site::factory()->for($deleteUser)->make([
                'title' => 'deleteUserSiteTitle',
                'url' => 'deleteUserSiteUrl',
                'description' => 'deleteUserSiteDescription',
            ]),
        ]);

        $deleteUser->delete();

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/games/aaa/sites');
        $response->assertStatus(200);

        $siteId1 = Site::find(1);
        $siteId2 = Site::find(2);
        $siteId3 = Site::find(3);
        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'loginUserSiteTitle',
                    'url' => 'loginUserSiteUrl',
                    'description' => 'loginUserSiteDescription',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => true,
                    'owner_name' => 'loginUser',
                    'game_title' => 'aaa',
                    'updated_at' => $siteId1->updated_at->timestamp,
                ],
                [
                    'id' => 2,
                    'title' => 'otherUserSiteTitle',
                    'url' => 'otherUserSiteUrl',
                    'description' => 'otherUserSiteDescription',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => false,
                    'owner_name' => 'otherUser',
                    'game_title' => 'aaa',
                    'updated_at' => $siteId2->updated_at->timestamp,
                ],
                [
                    'id' => 3,
                    'title' => 'deleteUserSiteTitle',
                    'url' => 'deleteUserSiteUrl',
                    'description' => 'deleteUserSiteDescription',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => false,
                    'owner_name' => null,
                    'game_title' => 'aaa',
                    'updated_at' => $siteId3->updated_at->timestamp,
                ],
            ]
        ]);
    }

    public function test正常系_お気に入りあり(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'aaa']);
        $game->sites()->save(
            $site = Site::factory()->for($user)->make([
                'title' => 'title',
                'url' => 'url',
                'description' => 'description',
            ])
        );

        $site->favorites()->save($user->favorites()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/games/aaa/sites');
        $response->assertStatus(200);

        $site = Site::first();
        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'title',
                    'url' => 'url',
                    'description' => 'description',
                    'favorites_count' => 1,
                    'favorited' => true,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => true,
                    'owner_name' => 'name',
                    'game_title' => 'aaa',
                    'updated_at' => $site->updated_at->timestamp,
                ],
            ]
        ]);
    }

    public function test正常系_いいねあり(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'aaa']);
        $game->sites()->save(
            $site = Site::factory()->for($user)->make([
                'title' => 'title',
                'url' => 'url',
                'description' => 'description',
            ])
        );

        $site->likes()->save($user->likes()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/games/aaa/sites');
        $response->assertStatus(200);

        $site = Site::first();
        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'title',
                    'url' => 'url',
                    'description' => 'description',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 1,
                    'liked' => true,
                    'owner' => true,
                    'owner_name' => 'name',
                    'game_title' => 'aaa',
                    'updated_at' => $site->updated_at->timestamp,
                ],
            ]
        ]);
    }

    public function test正常系_サイトなし(): void
    {
        $game = Game::factory()->create(['title' => 'aaa']);

        $response = $this->json('GET', 'api/games/aaa/sites');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [

            ]
        ]);
    }

    public function test異常系_対象のゲームなし(): void
    {
        $response = $this->json('GET', 'api/games/aaa/sites');
        $response->assertStatus(404);
    }
}
