<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_投稿記事複数(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->articles()->saveMany([
            Article::factory()->for($loginUser)->make([
                'title' => 'loginUserArticleTitle',
                'outline' => 'loginUserArticleOutline',
            ]),
            Article::factory()->for($loginUser)->make([
                'title' => 'loginUserArticleTitleTwo',
                'outline' => 'loginUserArticleOutlineTwo',
            ]),
            Article::factory()->for($otherUser)->make([
                'title' => 'otherUserArticleTitle',
                'outline' => 'otherUserArticleOutline',
            ]),
        ]);

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/posts/articles');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'loginUserArticleTitle',
                    'outline' => 'loginUserArticleOutline',
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
                    'title' => 'loginUserArticleTitleTwo',
                    'outline' => 'loginUserArticleOutlineTwo',
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
        $game->articles()->save(
            $article = Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $article->favorites()->save($user->favorites()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/articles');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'title',
                    'outline' => 'outline',
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
        $game->articles()->save(
            $article = Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $article->likes()->save($user->likes()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/games/game_title/articles');
        $response->assertStatus(200);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'title',
                    'outline' => 'outline',
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
        $game->articles()->save(
            Article::factory()->for($otherUser)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/articles');
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
        $game->articles()->save(
            Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $response = $this->json('GET', 'api/posts/articles');
        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->articles()->save(
            Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $this->actingAs($user);
        $response = $this->json('GET', 'api/posts/articles');
        $response->assertStatus(403);
    }
}
