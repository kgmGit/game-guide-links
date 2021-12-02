<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_記事作成者複数(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);
        $deleteUser = User::factory()->create(['name' => 'deleteUser']);

        $game = Game::factory()->create(['title' => 'aaa']);
        $game->articles()->saveMany([
            Article::factory()->for($loginUser)->make([
                'title' => 'loginUserArticleTitle',
                'outline' => 'loginUserArticleOutline',
            ]),
            Article::factory()->for($otherUser)->make([
                'title' => 'otherUserArticleTitle',
                'outline' => 'otherUserArticleOutline',
            ]),
            Article::factory()->for($deleteUser)->make([
                'title' => 'deleteUserArticleTitle',
                'outline' => 'deleteUserArticleOutline',
            ]),
        ]);

        $deleteUser->delete();

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/games/aaa/articles');
        $response->assertStatus(200);

        $articleId1 = Article::find(1);
        $articleId2 = Article::find(2);
        $articleId3 = Article::find(3);
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
                    'game_title' => 'aaa',
                    'updated_at' => $articleId1->updated_at->timestamp,
                ],
                [
                    'id' => 2,
                    'title' => 'otherUserArticleTitle',
                    'outline' => 'otherUserArticleOutline',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => false,
                    'owner_name' => 'otherUser',
                    'game_title' => 'aaa',
                    'updated_at' => $articleId2->updated_at->timestamp,
                ],
                [
                    'id' => 3,
                    'title' => 'deleteUserArticleTitle',
                    'outline' => 'deleteUserArticleOutline',
                    'favorites_count' => 0,
                    'favorited' => false,
                    'likes_count' => 0,
                    'liked' => false,
                    'owner' => false,
                    'owner_name' => null,
                    'game_title' => 'aaa',
                    'updated_at' => $articleId3->updated_at->timestamp,
                ],
            ]
        ]);
    }

    public function test正常系_お気に入りあり(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'aaa']);
        $game->articles()->save(
            $article = Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $article->favorites()->save($user->favorites()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/games/aaa/articles');
        $response->assertStatus(200);

        $article = Article::first();
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
                    'game_title' => 'aaa',
                    'updated_at' => $article->updated_at->timestamp,
                ],
            ]
        ]);
    }

    public function test正常系_いいねあり(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'aaa']);
        $game->articles()->save(
            $articles = Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $articles->likes()->save($user->likes()->make());

        $this->actingAs($user);
        $response = $this->json('GET', 'api/games/aaa/articles');
        $response->assertStatus(200);

        $article = Article::first();
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
                    'game_title' => 'aaa',
                    'updated_at' => $article->updated_at->timestamp,
                ],
            ]
        ]);
    }

    public function test正常系_サイトなし(): void
    {
        $game = Game::factory()->create(['title' => 'aaa']);

        $response = $this->json('GET', 'api/games/aaa/articles');
        $response->assertStatus(200);

        $response->assertJson([
            'data' => [

            ]
        ]);
    }

    public function test異常系_対象のゲームなし(): void
    {
        $response = $this->json('GET', 'api/games/aaa/articles');
        $response->assertStatus(404);
    }
}
