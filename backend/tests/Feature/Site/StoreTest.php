<?php

namespace Tests\Feature\Site;

use App\Http\Requests\Site\StoreRequest;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'url' => 'http://url.com',
            'description' => '概要'
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/sites', $body);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'title' => $body['title'],
                    'url' => $body['url'],
                    'description' => $body['description'],
                    'owner_name' => $user->name,
                    'game_title' => 'game_title'
                ]
            ]);

        /** @var Site $site */
        $site = Site::query()->first();
        $this->assertEquals(1, $site->id);
        $this->assertEquals($user->id, $site->user_id);
        $this->assertEquals($body['title'], $site->title);
        $this->assertEquals($body['url'], $site->url);
        $this->assertEquals($body['description'], $site->description);
    }

    public function test異常系_ゲームが存在しない(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'url' => 'http://url.com',
            'description' => '概要',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/not_exists_game_title/sites', $body);

        $response->assertStatus(404);

        $this->assertNull(Site::query()->first());
    }

    public function test異常系_未ログイン(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'url' => 'http://url.com',
            'description' => '概要',
        ];

        $response = $this->json('POST', 'api/games/game_title/sites', $body);

        $response->assertStatus(401);

        $this->assertNull(Site::query()->first());
    }

    public function test異常系_email未認証(): void
    {
        /** @var User $user */
        $user = User::factory()->unverified()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => 'site_title',
            'url' => 'http://url.com',
            'description' => '概要',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/sites', $body);

        $response->assertStatus(403);

        $this->assertNull(Site::query()->first());
    }

    public function test異常系_バリデーションエラー(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->games()->create(['title' => 'game_title']);

        $body = [
            'title' => '',
            'url' => 'http://url.com',
            'description' => '概要',
        ];

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games/game_title/sites', $body);

        $response->assertStatus(422);

        $this->assertNull(Site::query()->first());
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
            (new StoreRequest())->rules()
        );
        $this->assertEquals(
            $expect,
            $validator->passes()
        );
    }

    public function dataProviderValidation(): array
    {
        return [
            'OK_タイトル最小' => [
                [
                    'title' => 'a',
                    'url' => 'http://url.com',
                    'description' => '概要',
                ],
                true
            ],
            'OK_タイトル最大' => [
                [
                    'title' => Str::repeat('a', 20),
                    'url' => 'http://url.com',
                    'description' => '概要',
                ],
                true
            ],
            'OK_URL最大' => [
                [
                    'title' => 'title',
                    'url' => 'http://url.com' . Str::repeat('a', 255 - strlen('http://url.com')),
                    'description' => '概要',
                ],
                true
            ],
            'OK_概要最小' => [
                [
                    'title' => 'title',
                    'url' => 'http://url.com',
                    'description' => 'a',
                ],
                true
            ],
            'OK_概要最大' => [
                [
                    'title' => 'title',
                    'url' => 'http://url.com',
                    'description' => Str::repeat('a', 200),
                ],
                true
            ],
            'NG_タイトル最小未満' => [
                [
                    'title' => '',
                    'url' => 'http://url.com',
                    'description' => '概要',
                ],
                false
            ],
            'NG_タイトル最大超え' => [
                [
                    'title' => Str::repeat('a', 21),
                    'url' => 'http://url.com',
                    'description' => '概要',
                ],
                false
            ],
            'NG_URL最大超え' => [
                [
                    'title' => 'title',
                    'url' => 'http://url.com' . Str::repeat('a', 256 - strlen('http://url.com')),
                    'description' => '概要',
                ],
                false
            ],
            'NG_URLの形式が違う' => [
                [
                    'title' => 'title',
                    'url' => 'htt://url.com',
                    'description' => '概要',
                ],
                false
            ],
            'NG_概要最小未満' => [
                [
                    'title' => 'title',
                    'url' => 'http://url.com',
                    'description' => '',
                ],
                false
            ],
            'NG_概要最大超え' => [
                [
                    'title' => 'title',
                    'url' => 'http://url.com',
                    'description' => Str::repeat('a', 201),
                ],
                false
            ],
        ];
    }
}
