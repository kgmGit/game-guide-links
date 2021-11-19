<?php

namespace Tests\Feature\Site;

use App\Models\Favorite;
use App\Models\Like;
use App\Models\Report;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_作成したサイト(): void
    {
        $this->withoutExceptionHandling();
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Site::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/sites/1');

        $response->assertStatus(204);

        $this->assertNull(Site::query()->first());
    }

    public function test正常系_管理者(): void
    {
        /** @var User $adminUser */
        $adminUser = User::factory()->admin()->create();

        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        Site::factory()->for($user)->for($game)->create();

        $this->actingAs($adminUser);
        $response = $this->json('DELETE', 'api/games/game_title/sites/1');

        $response->assertStatus(204);

        $this->assertNull(Site::query()->first());
    }

    public function test正常系_サイトのお気に入り、いいね、通報が削除される(): void
    {
        $this->withoutExceptionHandling();
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $site = Site::factory()->for($user)->for($game)->create();

        $site->favorites()->save(
            $user->favorites()->make()
        );
        $site->likes()->save(
            $user->likes()->make()
        );
        Report::factory()->for($user)->for($site, 'reportable')->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/sites/1');

        $response->assertStatus(204);

        $this->assertNull(Site::query()->first());
        $this->assertNull(Favorite::query()->first());
        $this->assertNull(Like::query()->first());
        $this->assertNull(Report::query()->first());
    }

    public function test異常系_ゲームが存在しない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createSite = Site::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/not_exists_game_title/sites/1');

        $response->assertStatus(404);

        /** @var Site $site */
        $site = Site::query()->first();
        $this->assertEquals(1, $site->id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertEquals($createSite->title, $site->title);
        $this->assertEquals($createSite->url, $site->url);
        $this->assertEquals($createSite->description, $site->description);
    }

    public function test異常系_紐付いているゲームでない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createSite = Site::factory()->for($user)->for($game)->create();

        $user->games()->create(['title' => 'not_link_game_title']);

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/not_link_game_title/sites/1');

        $response->assertStatus(404);

        /** @var Site $site */
        $site = Site::query()->first();
        $this->assertEquals(1, $site->id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertEquals($createSite->title, $site->title);
        $this->assertEquals($createSite->url, $site->url);
        $this->assertEquals($createSite->description, $site->description);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createSite = Site::factory()->for($user)->for($game)->create();

        $response = $this->json('DELETE', 'api/games/game_title/sites/1');

        $response->assertStatus(401);

        /** @var Site $site */
        $site = Site::query()->first();
        $this->assertEquals(1, $site->id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertEquals($createSite->title, $site->title);
        $this->assertEquals($createSite->url, $site->url);
        $this->assertEquals($createSite->description, $site->description);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createSite = Site::factory()->for($user)->for($game)->create();

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/games/game_title/sites/1');

        $response->assertStatus(403);

        /** @var Site $site */
        $site = Site::query()->first();
        $this->assertEquals(1, $site->id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertEquals($createSite->title, $site->title);
        $this->assertEquals($createSite->url, $site->url);
        $this->assertEquals($createSite->description, $site->description);
    }

    public function test異常系_作成したユーザでない(): void
    {
        /** @var User $notCreateSiteUser */
        $notCreateSiteUser = User::factory()->create();

        $user = User::factory()->unverified()->create();
        $game = $user->games()->create(['title' => 'game_title']);
        $createSite = Site::factory()->for($user)->for($game)->create();

        $this->actingAs($notCreateSiteUser);
        $response = $this->json('DELETE', 'api/games/game_title/sites/1');

        $response->assertStatus(403);

        /** @var Site $site */
        $site = Site::query()->first();
        $this->assertEquals(1, $site->id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertEquals($createSite->title, $site->title);
        $this->assertEquals($createSite->url, $site->url);
        $this->assertEquals($createSite->description, $site->description);
    }
}
