<?php

namespace Tests\Feature\Like;

use App\Models\Article;
use App\Models\Game;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UnregisterTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_サイト(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create(['title' => 'title']);
        $user->likeSites()->attach($site->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/sites/1/like');

        $response->assertStatus(204);
        $likeSite = $user->likeSites()->first();
        $this->assertNull($likeSite);
    }

    public function test正常系_記事(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $article = Article::factory()->for($game)->create(['title' => 'title']);
        $user->likeArticles()->attach($user->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/articles/1/like');

        $response->assertStatus(204);
        $likeArticle = $user->likeArticles()->first();
        $this->assertNull($likeArticle);
    }

    public function test正常系_２回お気に入り解除しても何もしない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create(['title' => 'title']);
        $user->likeSites()->attach($site->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/sites/1/like');

        $response->assertStatus(204);
        $likeSite = $user->likeSites()->first();
        $this->assertNull($likeSite);

        $response = $this->json('DELETE', 'api/sites/1/like');

        $response->assertStatus(204);
        $likeSite = $user->likeSites()->first();
        $this->assertNull($likeSite);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create(['title' => 'title']);
        $user->likeSites()->attach($site->id);

        $response = $this->json('DELETE', 'api/sites/1/like');

        $response->assertStatus(401);
        $likeSite = $user->likeSites()->first();
        $this->assertEquals('title', $likeSite->title);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create(['title' => 'title']);
        $user->likeSites()->attach($site->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/sites/1/like');

        $response->assertStatus(403);
        $likeSite = $user->likeSites()->first();
        $this->assertEquals('title', $likeSite->title);
    }

    public function test異常系_存在しないサイト(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create(['title' => 'title']);
        $user->likeSites()->attach($site->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/sites/2/like');

        $response->assertStatus(404);
        $likeSite = $user->likeSites()->first();
        $this->assertEquals('title', $likeSite->title);
    }

    public function test異常系_存在しない記事(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $article = Article::factory()->for($game)->create(['title' => 'title']);
        $user->likeArticles()->attach($article->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/articles/2/like');

        $response->assertStatus(404);
        $likeArticle = $user->likeArticles()->first();
        $this->assertEquals('title', $likeArticle->title);
    }
}
