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

    public function test正常系(): void
    {
        /** @var User $loginUser */
        $loginUser = User::factory()->create(['name' => 'loginUser']);
        $otherUser = User::factory()->create(['name' => 'otherUser']);

        $game = Game::factory()->create(['title' => 'game_title']);
        $game->articles()->saveMany([
            Article::factory()->for($loginUser)->make([
                'title' => 'loginUserArticleTitle',
            ]),
            Article::factory()->for($loginUser)->make([
                'title' => 'loginUserArticleTitleTwo',
            ]),
            Article::factory()->for($otherUser)->make([
                'title' => 'otherUserArticleTitle',
            ]),
        ]);

        $this->actingAs($loginUser);
        $response = $this->json('GET', 'api/posts/articles');
        $response->assertStatus(200);

        $response->assertJsonCount(2, 'data')
            ->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'loginUserArticleTitle',
                    'owner' => true,
                    'owner_name' => 'loginUser',
                ],
                [
                    'id' => 2,
                    'title' => 'loginUserArticleTitleTwo',
                    'owner' => true,
                    'owner_name' => 'loginUser',
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
