<?php

namespace Tests\Feature\User;

use App\Models\Article;
use App\Models\Favorite;
use App\Models\Game;
use App\Models\Like;
use App\Models\Report;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'user');

        $response->assertStatus(204);

        $this->assertGuest('web');

        $this->assertNull(User::first());
    }

    public function test正常系_削除ユーザの所有ゲーム、サイト、記事は削除されない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        Site::factory()->for($user)->for($game)->create();
        Article::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'user');

        $response->assertStatus(204);

        $this->assertGuest('web');

        $this->assertNull(User::query()->first());

        $game = Game::query()->first();
        $this->assertNotNull($game);
        $this->assertNull($game->user_id);
        $site = Site::query()->first();
        $this->assertNotNull($site);
        $this->assertNull($site->user_id);
        $article = Article::query()->first();
        $this->assertNotNull($article);
        $this->assertNull($article->user_id);
    }

    public function test正常系_削除ユーザのお気に入り、いいね、通報が削除される(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->for($user)->create();
        $site = Site::factory()->for($user)->for($game)->create();
        $article = Article::factory()->for($user)->for($game)->create();

        $game->favorites()->save($user->favorites()->make());
        Report::factory()->for($user)->for($game, 'reportable')->create();
        $site->favorites()->save($user->favorites()->make());
        $site->likes()->save($user->likes()->make());
        Report::factory()->for($user)->for($site, 'reportable')->create();
        $article->favorites()->save($user->favorites()->make());
        $article->likes()->save($user->likes()->make());
        Report::factory()->for($user)->for($article, 'reportable')->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'user');

        $response->assertStatus(204);

        $this->assertGuest('web');

        $this->assertNull(User::query()->first());

        $this->assertNull(Favorite::query()->first());
        $this->assertNull(Like::query()->first());
        $this->assertNull(Report::query()->first());
    }
}
