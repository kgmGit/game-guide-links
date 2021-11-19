<?php

namespace Tests\Feature\Site;

use App\Models\Game;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_投稿サイト複数(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->saveMany([
            Site::factory()->for($loginUser)->make([
                'title' => 'loginUserSiteTitle',
                'url' => 'loginUserSiteUrl',
                'description' => 'loginUserSiteDescription',
            ]),
            Site::factory()->for($loginUser)->make([
                'title' => 'loginUserSiteTitleTwo',
                'url' => 'loginUserSiteUrlTwo',
                'description' => 'loginUserSiteDescriptionTwo',
            ]),
            Site::factory()->for($otherUser)->make([
                'title' => 'otherUserSiteTitle',
                'url' => 'otherUserSiteUrl',
                'description' => 'otherUserSiteDescription',
            ]),
        ]);

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/posts/sites');
        $response->assertStatus(200);

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
                    'game_title' => 'game_title',
                ],
                [
                    'id' => 2,
                    'title' => 'loginUserSiteTitleTwo',
                    'url' => 'loginUserSiteUrlTwo',
                    'description' => 'loginUserSiteDescriptionTwo',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => true,
                    'owner_name' => 'loginUser',
                    'game_title' => 'game_title',
                ],
            ]
        ]);
    }

    public function test正常系_お気に入りあり(): void
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

        $site->favorites()->save($user->favorites()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/sites');
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
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => true,
                    'owner_name' => 'name',
                    'game_title' => 'game_title'
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

        $site->likes()->save($user->likes()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/games/game_title/sites');
        $response->assertStatus(200);

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
                    'game_title' => 'game_title',
                ],
            ]
        ]);
    }

    public function test正常系_投稿サイトなし(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->save(
            Site::factory()->for($otherUser)->make([
                'title' => 'title',
                'url' => 'url',
                'description' => 'description',
            ])
        );

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/sites');
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
            Site::factory()->for($user)->make([
                'title' => 'title',
                'url' => 'url',
                'description' => 'description',
            ])
        );

        $response = $this->json('GET', 'api/posts/sites');
        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->sites()->save(
            Site::factory()->for($user)->make([
                'title' => 'title',
                'url' => 'url',
                'description' => 'description',
            ])
        );

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/sites');
        $response->assertStatus(403);
    }
}
