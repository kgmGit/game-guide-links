<?php

namespace Tests\Feature\Like;

use App\Models\Article;
use App\Models\Game;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_サイト(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/sites/1/like');

        $response->assertStatus(204);
        $likeSite = $user->likeSites()->first();
        $this->assertEquals('title', $likeSite->title);
    }

    public function test正常系_記事(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Article::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/articles/1/like');

        $response->assertStatus(204);
        $likeArticle = $user->likeArticles()->first();
        $this->assertEquals('title', $likeArticle->title);
    }

    public function test正常系_２回いいね登録しても何もしない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/sites/1/like');

        $response->assertStatus(204);
        $likeSite = $user->likeSites()->first();
        $this->assertEquals('title', $likeSite->title);

        $response = $this->json('PUT', 'api/sites/1/like');

        $response->assertStatus(204);
        $likeSite = $user->likeSites()->first();
        $this->assertEquals('title', $likeSite->title);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create(['title' => 'title']);

        $response = $this->json('PUT', 'api/sites/1/like');

        $response->assertStatus(401);
        $likeSite = $user->likeSites()->first();
        $this->assertNull($likeSite);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/sites/1/like');

        $response->assertStatus(403);
        $likeSite = $user->likeSites()->first();
        $this->assertNull($likeSite);
    }

    public function test異常系_存在しないサイト(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/sites/2/like');

        $response->assertStatus(404);
        $likeSite = $user->likeSites()->first();
        $this->assertNull($likeSite);
    }

    public function test異常系_存在しない記事(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Article::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/articles/2/like');

        $response->assertStatus(404);
        $likeArticle = $user->likeSites()->first();
        $this->assertNull($likeArticle);
    }
}
