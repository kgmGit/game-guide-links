<?php

namespace Tests\Feature\Game;

use App\Http\Requests\Game\StoreRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test正常系(): void
    {
        $this->withoutExceptionHandling();
        $body = [
            'title' => 'title',
        ];

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games', $body);
        $response->assertStatus(201);

        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'title',
                'favorites_count' => 0,
                'favorited' => false,
            ]
        ]);
    }

    public function test異常系_未ログイン(): void
    {
        $body = [
            'title' => 'title',
        ];

        $response = $this->json('POST', 'api/games', $body);
        $response->assertStatus(401);
    }

    public function test異常系_email未認証(): void
    {
        $body = [
            'title' => 'title',
        ];

        $unverifiedUser = User::factory()->unverified()->create();

        $this->actingAs($unverifiedUser);
        $response = $this->json('POST', 'api/games', $body);
        $response->assertStatus(403);
    }

    public function test異常系_タイトル重複(): void
    {
        $body = [
            'title' => 'title',
        ];

        /** @var User $user */
        $user = User::factory()->create();

        $user->games()->create($body);

        $this->actingAs($user);
        $response = $this->json('POST', 'api/games', $body);
        $response->assertStatus(422);

        $response->assertJson([
            'errors' => [
                'title' => [
                    'すでに登録されています',
                ],
            ]
        ]);
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
                ],
                true
            ],
            'OK_タイトル最大' => [
                [
                    'title' => Str::repeat('a', 30),
                ],
                true
            ],
            'NG_タイトル最小未満' => [
                [
                    'title' => '',
                ],
                false
            ],
            'NG_タイトル最大超え' => [
                [
                    'title' => Str::repeat('a', 31),
                ],
                false
            ],
        ];
    }
}
