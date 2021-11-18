<?php

namespace Tests\Feature\Game;

use App\Models\Article;
use App\Models\Favorite;
use App\Models\Game;
use App\Models\Report;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_所有するゲーム(): void
    {
        $this->withoutExceptionHandling();
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(204);
        $this->assertNull(Game::query()->first());
    }

    public function test正常系_管理者(): void
    {
        /** @var User $user */
        $adminUser = User::factory()->admin()->create();
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $this->actingAs($adminUser);
        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(204);
        $this->assertNull(Game::query()->first());
    }

    public function test正常系_ゲームのお気に入り、通報が削除される(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $game->favorites()->save(
            $user->favorites()->make()
        );
        Report::factory()->for($user)->for($game, 'reportable')->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(204);
        $this->assertNull(Game::query()->first());
        $this->assertNull(Favorite::query()->first());
        $this->assertNull(Report::query()->first());
    }

    public function test異常系_ゲームが存在しない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/' . 'not_exists_game_title');

        $response->assertStatus(404);
        $this->assertNotNull(Game::query()->first());
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(401);
        $this->assertNotNull(Game::query()->first());
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $user->games()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(403);
        $this->assertNotNull(Game::query()->first());
    }

    public function test異常系_所有するゲームでない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUser->games()->create(['title' => 'game_title']);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(403);
        $this->assertNotNull(Game::query()->first());
    }

    public function test異常系_サイトが紐付いている(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Site::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(422);
        $this->assertNotNull(Game::query()->first());
    }

    public function test異常系_記事が紐付いている(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Article::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/' . 'game_title');

        $response->assertStatus(422);
        $this->assertNotNull(Game::query()->first());
    }
}
