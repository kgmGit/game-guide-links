<?php

namespace Tests\Feature\Report;

use App\Models\Article;
use App\Models\Game;
use App\Models\Report;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user  */
        $user = User::factory()->admin()->create(['name' => 'user_name']);
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create();
        $article = Article::factory()->for($game)->create();

        $game->createReport($user, 'game_report');
        $site->createReport($user, 'site_report');
        $article->createReport($user, 'article_report');

        $this->actingAs($user);
        $response = $this->json('GET', 'api/reports');

        $response->dump();

        $reportId1 = Report::find(1);
        $reportId2 = Report::find(2);
        $reportId3 = Report::find(3);
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => 1,
                        'reportable_type' => Game::class,
                        'reportable_id' => '1',
                        'content' => 'game_report',
                        'user_name' => $user->name,
                        'game_title' => $game->title,
                        'created_at' => $reportId1->created_at->timestamp,
                    ],
                    [
                        'id' => 2,
                        'reportable_type' => Site::class,
                        'reportable_id' => '1',
                        'content' => 'site_report',
                        'user_name' => $user->name,
                        'game_title' => $game->title,
                        'created_at' => $reportId2->created_at->timestamp,
                    ],
                    [
                        'id' => 3,
                        'reportable_type' => Article::class,
                        'reportable_id' => '1',
                        'content' => 'article_report',
                        'user_name' => $user->name,
                        'game_title' => $game->title,
                        'created_at' => $reportId3->created_at->timestamp,
                    ],
                ]
                ]);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user  */
        $user = User::factory()->admin()->create(['name' => 'user_name']);
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create();
        $article = Article::factory()->for($game)->create();

        $game->createReport($user, 'game_report');
        $site->createReport($user, 'site_report');
        $game->createReport($user, 'article_report');

        $response = $this->json('GET', 'api/reports');

        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user  */
        $user = User::factory()->unverified()->admin()->create(['name' => 'user_name']);
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create();
        $article = Article::factory()->for($game)->create();

        $game->createReport($user, 'game_report');
        $site->createReport($user, 'site_report');
        $game->createReport($user, 'article_report');

        $this->actingAs($user);
        $response = $this->json('GET', 'api/reports');

        $response->assertStatus(403);
    }

    public function test異常系_管理者ユーザでない(): void
    {
        /** @var User $user  */
        $user = User::factory()->create(['name' => 'user_name']);
        $game = Game::factory()->create(['title' => 'game_title']);
        $site = Site::factory()->for($game)->create();
        $article = Article::factory()->for($game)->create();

        $game->createReport($user, 'game_report');
        $site->createReport($user, 'site_report');
        $game->createReport($user, 'article_report');

        $this->actingAs($user);
        $response = $this->json('GET', 'api/reports');

        $response->assertStatus(403);
    }
}
