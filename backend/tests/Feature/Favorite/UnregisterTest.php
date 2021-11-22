<?php

namespace Tests\Feature\Favorite;

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

    public function test正常系_ゲーム(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $user->favoriteGames()->attach($game->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/favorite');

        $response->assertStatus(204);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertNull($favoriteGame);
    }

    public function test正常系_サイト(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create(['title' => 'title']);
        $user->favoriteSites()->attach($site->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/sites/1/favorite');

        $response->assertStatus(204);
        $favoriteSite = $user->favoriteSites()->first();
        $this->assertNull($favoriteSite);
    }

    public function test正常系_記事(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $article = Article::factory()->for($game)->create(['title' => 'title']);
        $user->favoriteArticles()->attach($user->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/articles/1/favorite');

        $response->assertStatus(204);
        $favoriteArticle = $user->favoriteArticles()->first();
        $this->assertNull($favoriteArticle);
    }

    public function test正常系_２回お気に入り解除しても何もしない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $user->favoriteGames()->attach($game->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/favorite');

        $response->assertStatus(204);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertNull($favoriteGame);

        $response = $this->json('DELETE', 'api/games/game_title/favorite');

        $response->assertStatus(204);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertNull($favoriteGame);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $user->favoriteGames()->attach($game->id);

        $response = $this->json('DELETE', 'api/games/game_title/favorite');

        $response->assertStatus(401);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertEquals('game_title', $favoriteGame->title);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $user->favoriteGames()->attach($game->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/favorite');

        $response->assertStatus(403);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertEquals('game_title', $favoriteGame->title);
    }

    public function test異常系_存在しないゲーム(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $user->favoriteGames()->attach($game->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/not_exist_game_title/favorite');

        $response->assertStatus(404);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertEquals('game_title', $favoriteGame->title);
    }

    public function test異常系_存在しないサイト(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create(['title' => 'title']);
        $user->favoriteSites()->attach($site->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/sites/2/favorite');

        $response->assertStatus(404);
        $favoriteSite = $user->favoriteSites()->first();
        $this->assertEquals('title', $favoriteSite->title);
    }

    public function test異常系_存在しない記事(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        $article = Article::factory()->for($game)->create(['title' => 'title']);
        $user->favoriteArticles()->attach($article->id);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/articles/2/favorite');

        $response->assertStatus(404);
        $favoriteArticle = $user->favoriteArticles()->first();
        $this->assertEquals('title', $favoriteArticle->title);
    }
}
