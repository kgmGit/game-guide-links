<?php

namespace Tests\Feature\Article;

use App\Models\Article;
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
        $deleteUser = User::factory()->create(['name' => 'deleteUser']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->articles()->saveMany([
            $favoriteArticle1 = Article::factory()->for($otherUser)->make([
                'title' => 'otherUserArticleTitle',
            ]),
            Article::factory()->for($otherUser)->make([
                'title' => 'notFavoriteSiteTitle',
            ]),
            $favoriteArticle2 = Article::factory()->for($deleteUser)->make([
                'title' => 'deleteUserSiteTitle',
            ]),
        ]);
        $loginUser->favorites()->saveMany([
            $favoriteArticle1->favorites()->make(),
            $favoriteArticle2->favorites()->make(),
        ]);

        $deleteUser->delete();

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/favorites/articles');
        $response->assertStatus(200);

        $response->assertJsonCount(2, 'data')
            ->assertJson([
                'data' => [
                    [
                        'id' => 1,
                        'title' => 'otherUserArticleTitle',
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
        $game->articles()->save(
            Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $this->actingAs($user);
        $response = $this->json('GET', 'api/favorites/articles');
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
            $articles = Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $user->favorites()->save($articles->favorites()->make());

        $response = $this->json('GET', 'api/favorites/articles');
        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create(['name' => 'name']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->articles()->save(
            $article = Article::factory()->for($user)->make([
                'title' => 'title',
                'outline' => 'outline',
            ])
        );

        $this->actingAs($user);
        $user->favorites()->save($article->favorites()->make());

        $response = $this->json('GET', 'api/favorites/articles');
        $response->assertStatus(403);
    }
}
