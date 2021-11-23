<?php

namespace Tests\Feature\Report;

use App\Http\Requests\Report\ReportRequest;
use App\Models\Article;
use App\Models\Game;
use App\Models\Report;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系_ゲーム(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $body = [
            'content' => 'content',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/report', $body);

        $response->assertStatus(201)
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'reportable_type' => Game::class,
                    'reportable_id' => 1,
                    'content' => $body['content'],
                    'user_name' => $user->name,
                    'game_title' => 'game_title',
                ],
            ]);
        $report = Report::first();
        $this->assertEquals(1, $report->id);
        $this->assertEquals(Game::class, $report->reportable_type);
        $this->assertEquals(1, $report->reportable_id);
        $this->assertEquals($body['content'], $report->content);
    }

    public function test正常系_サイト(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Site::factory()->for($game)->create();

        $body = [
            'content' => 'content',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/sites/1/report', $body);

        $response->assertStatus(201)
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'reportable_type' => Site::class,
                    'reportable_id' => 1,
                    'content' => $body['content'],
                    'user_name' => $user->name,
                    'game_title' => 'game_title',
                ],
            ]);
        $report = Report::first();
        $this->assertEquals(1, $report->id);
        $this->assertEquals(Site::class, $report->reportable_type);
        $this->assertEquals(1, $report->reportable_id);
        $this->assertEquals($body['content'], $report->content);
    }

    public function test正常系_記事(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $game = Game::factory()->create(['title' => 'game_title']);
        Article::factory()->for($game)->create();

        $body = [
            'content' => 'content',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/articles/1/report', $body);

        $response->assertStatus(201)
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'reportable_type' => Article::class,
                    'reportable_id' => 1,
                    'content' => $body['content'],
                    'user_name' => $user->name,
                    'game_title' => 'game_title',
                ],
            ]);
        $report = Report::first();
        $this->assertEquals(1, $report->id);
        $this->assertEquals(Article::class, $report->reportable_type);
        $this->assertEquals(1, $report->reportable_id);
        $this->assertEquals($body['content'], $report->content);
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $body = [
            'content' => 'content',
        ];

        $response = $this->json('POST', 'api/games/game_title/report', $body);

        $response->assertStatus(401);

        $report = Report::first();
        $this->assertNull($report);
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        Game::factory()->create(['title' => 'game_title']);

        $body = [
            'content' => 'content',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/report', $body);

        $response->assertStatus(403);

        $report = Report::first();
        $this->assertNull($report);
    }

    public function test異常系_存在しないゲーム(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $body = [
            'content' => 'content',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/not_exist_game_title/report', $body);

        $response->assertStatus(404);

        $report = Report::first();
        $this->assertNull($report);
    }

    public function test異常系_存在しないサイト(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $body = [
            'content' => 'content',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/sites/1/report', $body);

        $response->assertStatus(404);

        $report = Report::first();
        $this->assertNull($report);
    }

    public function test異常系_存在しない記事(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $body = [
            'content' => 'content',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/articles/1/report', $body);

        $response->assertStatus(404);

        $report = Report::first();
        $this->assertNull($report);
    }

    public function test異常系_バリデーションエラー(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Game::factory()->create(['title' => 'game_title']);

        $body = [
            'content' => '',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/report', $body);

        $response->assertStatus(422);

        $report = Report::first();
        $this->assertNull($report);
    }

    /**
     * @param array $data
     * @param bool $expect
     * @dataProvider dataProviderValidation
     */
    public function testバリデーション(array $data, bool $expect): void
    {
        $validator = Validator::make(
            $data,
            (new ReportRequest())->rules()
        );
        $this->assertEquals(
            $expect,
            $validator->passes()
        );
    }

    public function dataProviderValidation(): array
    {
        return [
            'OK_内容最小' => [
                [
                    'content' => 'a',
                ],
                true
            ],
            'OK_内容最大' => [
                [
                    'content' => Str::repeat('a', 255),
                ],
                true
            ],
            'NG_内容最小未満' => [
                [
                    'content' => '',
                ],
                false
            ],
            'NG_内容最大超え' => [
                [
                    'content' => Str::repeat('a', 256),
                ],
                false
            ],
        ];
    }
}
