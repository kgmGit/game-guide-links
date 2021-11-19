<?php

namespace Tests\Feature\Article;

use App\Models\Article;
use App\Models\Favorite;
use App\Models\Like;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_作成した記事(): void
    {
        $this->withoutExceptionHandling();
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/articles/1');

        $response->assertStatus(204);

        $this->assertNull(Article::query()->first());
    }

    public function test正常系_管理者(): void
    {
        /** @var User $adminUser */
        $adminUser = User::factory()->admin()->create();

        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create();

        $this->actingAs($adminUser);
        $response = $this->json('DELETE', 'api/games/game_title/articles/1');

        $response->assertStatus(204);

        $this->assertNull(Article::query()->first());
    }

    public function test正常系_記事のお気に入り、いいね、通報が削除される(): void
    {
        $this->withoutExceptionHandling();
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $article = Article::factory()->for($user)->for($game)->create();

        $article->favorites()->save(
            $user->favorites()->make()
        );
        $article->likes()->save(
            $user->likes()->make()
        );
        Report::factory()->for($user)->for($article, 'reportable')->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/articles/1');

        $response->assertStatus(204);

        $this->assertNull(Article::query()->first());
        $this->assertNull(Favorite::query()->first());
        $this->assertNull(Like::query()->first());
        $this->assertNull(Report::query()->first());
    }

    public function test異常系_ゲームが存在しない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createArticle = Article::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/not_exists_game_title/articles/1');

        $response->assertStatus(404);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($createArticle->title, $article->title);
        $this->assertEquals($createArticle->outline, $article->outline);
        $this->assertEquals($createArticle->content, $article->content);
    }

    public function test異常系_紐付いているゲームでない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createArticle = Article::factory()->for($user)->for($game)->create();

        $user->games()->create(['title' => 'not_link_game_title']);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/not_link_game_title/articles/1');

        $response->assertStatus(404);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($createArticle->title, $article->title);
        $this->assertEquals($createArticle->outline, $article->outline);
        $this->assertEquals($createArticle->content, $article->content);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createArticle = Article::factory()->for($user)->for($game)->create();

        $response = $this->json('DELETE', 'api/games/game_title/articles/1');

        $response->assertStatus(401);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($createArticle->title, $article->title);
        $this->assertEquals($createArticle->outline, $article->outline);
        $this->assertEquals($createArticle->content, $article->content);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createArticle = Article::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/articles/1');

        $response->assertStatus(403);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($createArticle->title, $article->title);
        $this->assertEquals($createArticle->outline, $article->outline);
        $this->assertEquals($createArticle->content, $article->content);
    }

    public function test異常系_作成したユーザでない(): void
    {
        /** @var User $notCreateArticleUser */
        $notCreateArticleUser = User::factory()->create();

        $user = User::factory()->unverified()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createArticle = Article::factory()->for($user)->for($game)->create();

        $this->actingAs($notCreateArticleUser);
        $response = $this->json('DELETE', 'api/games/game_title/articles/1');

        $response->assertStatus(403);

        /** @var Article $article */
        $article = Article::query()->first();
        $this->assertEquals(1, $article->id);
        $this->assertEquals($user->id, $article->user_id);
        $this->assertEquals($createArticle->title, $article->title);
        $this->assertEquals($createArticle->outline, $article->outline);
        $this->assertEquals($createArticle->content, $article->content);
    }
}
