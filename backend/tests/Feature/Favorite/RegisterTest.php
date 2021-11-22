<?php

namespace Tests\Feature\Favorite;

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

    public function test正常系_ゲーム(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/games/game_title/favorite');

        $response->assertStatus(204);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertEquals('game_title', $favoriteGame->title);
    }

    public function test正常系_サイト(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/sites/1/favorite');

        $response->assertStatus(204);
        $favoriteSite = $user->favoriteSites()->first();
        $this->assertEquals('title', $favoriteSite->title);
    }

    public function test正常系_記事(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Article::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/articles/1/favorite');

        $response->assertStatus(204);
        $favoriteArticle = $user->favoriteArticles()->first();
        $this->assertEquals('title', $favoriteArticle->title);
    }

    public function test正常系_２回お気に入り登録しても何もしない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/games/game_title/favorite');

        $response->assertStatus(204);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertEquals('game_title', $favoriteGame->title);

        $response->assertStatus(204);
        $response = $this->json('PUT', 'api/games/game_title/favorite');
        $favoriteGames = $user->favoriteGames()->get();
        $this->assertCount(1, $favoriteGames);
        $this->assertEquals('game_title', $favoriteGames[0]->title);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $response = $this->json('PUT', 'api/games/game_title/favorite');

        $response->assertStatus(401);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertNull($favoriteGame);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        Game::factory()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/games/game_title/favorite');

        $response->assertStatus(403);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertNull($favoriteGame);
    }

    public function test異常系_存在しないゲーム(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        Game::factory()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/games/not_exist_game_title/favorite');

        $response->assertStatus(404);
        $favoriteGame = $user->favoriteGames()->first();
        $this->assertNull($favoriteGame);
    }

    public function test異常系_存在しないサイト(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/sites/2/favorite');

        $response->assertStatus(404);
        $favoriteSite = $user->favoriteSites()->first();
        $this->assertNull($favoriteSite);
    }

    public function test異常系_存在しない記事(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Article::factory()->for($game)->create(['title' => 'title']);

        $this->actingAs($user);
        $response = $this->json('PUT', 'api/articles/2/favorite');

        $response->assertStatus(404);
        $favoriteArticle = $user->favoriteSites()->first();
        $this->assertNull($favoriteArticle);
    }
}
