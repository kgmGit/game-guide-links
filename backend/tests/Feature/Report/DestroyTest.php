<?php

namespace Tests\Feature\Report;

use App\Models\Game;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create();
        $game = Game::factory()->create();
        $game->createReport($user, 'content');

        $this->assertNotNull(Report::first());

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/reports/1');

        $response->assertStatus(204);
        $this->assertNull(Report::first());
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create();
        $game = Game::factory()->create();
        $game->createReport($user, 'content');

        $this->assertNotNull(Report::first());

        $response = $this->json('DELETE', 'api/reports/1');

        $response->assertStatus(401);
        $this->assertNotNull(Report::first());
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->admin()->create();
        $game = Game::factory()->create();
        $game->createReport($user, 'content');

        $this->assertNotNull(Report::first());

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/reports/1');

        $response->assertStatus(403);
        $this->assertNotNull(Report::first());
    }

    public function test異常系_管理者ユーザでない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create();
        $game->createReport($user, 'content');

        $this->assertNotNull(Report::first());

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/reports/1');

        $response->assertStatus(403);
        $this->assertNotNull(Report::first());
    }

    public function test異常系_存在しない通報(): void
    {
        /** @var User $user */
        $user = User::factory()->admin()->create();
        $game = Game::factory()->create();
        $game->createReport($user, 'content');

        $this->assertNotNull(Report::first());

        $this->actingAs($user);
        $response = $this->json('DELETE', 'api/reports/2');

        $response->assertStatus(404);
        $this->assertNotNull(Report::first());
    }
}
